<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;
use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * Controller for actions related to Institution Specializations
 *
 * @author Allejo Chris G. Velarde
 *
 */
class SpecializationController extends InstitutionAwareController
{
    /**
     * @var InstitutionMedicalCenter
     */
    protected $institutionMedicalCenter;

    /**
     * @var InstitutionSpecialization
     */
    protected $institutionSpecialization;

    public function preExecute()
    {
        parent::preExecute();

        if ($imcId=$this->getRequest()->get('imcId',0)) {
            $this->institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')
                ->find($imcId);

            // non-existent medical center group
            if (!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid medical center.');
            }

            // medical center group does not belong to this institution
            if ($this->institutionMedicalCenter->getInstitution()->getId() != $this->institution->getId()) {
                 return new Response('Medical center does not belong to institution', 401);
            }
        }

        if($isId = $this->getRequest()->get('isId', 0)) {
            $this->institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($isId);
        }
    }

    private function _getEditMedicalCenterCalloutView()
    {
        $calloutParams = array(
                        '{CENTER_NAME}' => $this->institutionMedicalCenter->getName(),
                        '{ADD_CLINIC_URL}' => $this->generateUrl('institution_medicalCenter_add')
        );
        $calloutMessage = $this->get('services.institution.callouts')->get('success_edit_center', $calloutParams);
        $calloutView = $this->renderView('InstitutionBundle:Widgets:callout.html.twig', array('callout' => $calloutMessage));

        return $calloutView;
    }

    /**
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxLoadSpecializationTreatmentsAction(Request $request)
    {
        $selectedTreatments = array();
        $specializationId = $request->get('specializationId');

        if($this->institutionSpecialization && $this->institutionSpecialization->getTreatments()) {
            foreach($this->institutionSpecialization->getTreatments() as $treatment) {
                $selectedTreatments[] = $treatment->getId();
            }
        }
        $form = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization());

        $specializationTreatments = $this->get('services.treatment_bundle')->getTreatmentsBySpecializationIdGroupedBySubSpecialization($specializationId);

        $html = $this->renderView('InstitutionBundle:Specialization/Widgets:form.specializationTreatments.html.twig', array(
            'form' => $form->createView(),
            'formName' => InstitutionSpecializationFormType::NAME,
            'specializationId' => $specializationId,
            'selectedTreatments' => $selectedTreatments,
            'specializationTreatments' => $specializationTreatments,
            'isId' => $this->institutionSpecialization ? $this->institutionSpecialization->getId() : null,
        ));

        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
    }

    public function ajaxAddSpecializationAction(Request $request)
    {
        //Multiple centers of an institution with similar specializations are now allowed.
        //$specializations = $this->get('services.institution_specialization')->getNotSelectedSpecializations($this->institution);
        $specializations = $this->get('services.institution_specialization')->getNotSelectedSpecializationsOfInstitutionMedicalCenter($this->institutionMedicalCenter);

        $params =  array(
            'imcId' => $this->institutionMedicalCenter->getId(),
            'specializations' => $specializations,
            'saveFormAction' => $this->generateUrl('institution_ajaxSaveSpecializations', array('imcId' => $this->institutionMedicalCenter->getId())),
            'buttonLabel' => 'Save'
        );

        $html = $this->renderView('InstitutionBundle:Specialization/Widgets:form.multipleAdd.html.twig', $params);
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
    }

    /**
     * Save Specializations for Clinics Profile
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function saveSpecializationsAction(Request $request)
    {
        //Multiple centers of an institution with similar specializations are now allowed.
        //$specializations = $this->get('services.institution_specialization')->getNotSelectedSpecializations($this->institution);
        $specializations = $this->get('services.institution_specialization')->getNotSelectedSpecializationsOfInstitutionMedicalCenter($this->institutionMedicalCenter);

        if ($request->isMethod('POST')) {

            $specializationsWithTreatments = $request->get(InstitutionSpecializationFormType::NAME);

            if (\count($specializationsWithTreatments)) {

                $isIds = $this->get('services.institution_medical_center')->addMedicalCenterSpecializationsWithTreatments($this->institutionMedicalCenter, $specializationsWithTreatments);
                if(!empty($isIds)){
                    foreach ($this->institutionMedicalCenter->getInstitutionSpecializations() as $institutionSpecialization) {
                        if (in_array($institutionSpecialization->getId(), $isIds)) {
                            $html['html'][] = $this->renderView('InstitutionBundle:MedicalCenter:listItem.institutionSpecializationTreatments.html.twig', array(
                                'each' => $institutionSpecialization,
                                'institutionMedicalCenter' => $this->institutionMedicalCenter,
                                'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView(),
                            ));
                        }
                    }

                    // Invalidate InstitutionMedicalCenterProfile memcache
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

                    // Invalidate InstitutionProfile memcache
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));

                    $response = new Response(\json_encode($html), 200, array('content-type' => 'application/json'));
                }
            } else {

                $response = new Response('Please select at least one treatment.', 400);
            }
        }

        return $response;
    }
    /**
     * Edit Specialization treatments under clinic profile page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxAddInstitutionSpecializationTreatmentsAction(Request $request)
    {
        $institutionSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $institutionSpecialization = $institutionSpecializationRepo->find($request->get('isId'));
        if (!$institutionSpecialization ) {
            throw $this->createNotFoundException('Invalid institution specialization');
        }

        if ($request->isMethod('POST')) {

            $specializationsWithTreatments = $request->get(InstitutionSpecializationFormType::NAME);

            // Delete Treatments
            if($deleteTreatmentIds = $request->get('deleteTreatments')) {
                $institutionSpecializationRepo->deleteBySpecializationIdAndTreatmentIds($institutionSpecialization->getId(), $deleteTreatmentIds);
            }
            if (\count($specializationsWithTreatments)) {

                $this->get('services.institution_medical_center')->addMedicalCenterSpecializationsWithTreatments($this->institutionMedicalCenter, $specializationsWithTreatments);

                $responseContent = \json_encode(array('specializations' =>$specializationsWithTreatments));

                // Invalidate InstitutionMedicalCenterProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

                // Invalidate InstitutionProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));

                $response = new Response($responseContent, 200, array('content-type' => 'application/json'));

            } else {
                $response = new Response('Unable top edit Treatments', 404);
            }
        }

        return $response;
    }

    /**
     * Remove institution specialization
     *
     * @param Request $request
     */
    public function ajaxRemoveSpecializationAction(Request $request)
    {
        $institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')
        ->find($request->get('isId', 0));
        if (!$institutionSpecialization) {
            throw $this->createNotFoundException('Invalid instituiton specialization');
        }

        if ($institutionSpecialization->getInstitutionMedicalCenter()->getId() != $this->institutionMedicalCenter->getId()) {
            return new Response("Cannot remove specialization that does not belong to this institution", 401);
        }

        $form = $this->createForm(new CommonDeleteFormType(), $institutionSpecialization);

        if ($request->isMethod('POST'))  {
            $form->bind($request);
            if ($form->isValid()) {
                $_id = $institutionSpecialization->getId();
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($institutionSpecialization);
                $em->flush();

                // Invalidate InstitutionMedicalCenterProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

                // Invalidate InstitutionProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));

                $responseContent = array('id' => $_id);
                $response = new Response(\json_encode($responseContent), 200, array('content-type' => 'application/json'));
            }
            else {
                $response = new Response("Invalid form", 400);
            }
        }

        return $response;
    }

}