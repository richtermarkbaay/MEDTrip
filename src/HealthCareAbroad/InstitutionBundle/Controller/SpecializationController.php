<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

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
        
        $this->institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')
            ->find($this->getRequest()->get('isId', 0));
        
    }
    
    /**
     * Remove a Treatmnent from an institution specialization
     * Expected parameters
     *     
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxRemoveSpecializationTreatmentAction(Request $request)
    {
        $treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($request->get('tId'));
        if (!$this->institutionSpecialization) {
            throw $this->createNotFoundException("Invalid institution specialization {$this->institutionSpecialization->getId()}.");
        }
        if (!$treatment) {
            throw $this->createNotFoundException("Invalid treatment {$treatment->getId()}.");
        }

        if ($request->isMethod('POST'))  {
            
            $this->institutionSpecialization->removeTreatment($treatment);
            $form = $this->createForm(new CommonDeleteFormType(), $this->institutionSpecialization);
            
            $form->bind($request);
            if ($form->isValid()) {
                
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($this->institutionSpecialization);
                $em->flush();
                $responseContent = array('calloutView' => $this->_getEditMedicalCenterCalloutView(), 'id' => $treatment->getId());
                $response = new Response(\json_encode($responseContent), 200, array('content-type' => 'application/json'));
            }
            else {
                $response = new Response("Invalid form", 400);
            }
        }

        return $response;
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
     * Load specializations in clninc profile
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Chaztine Blance
     */
    public function ajaxLoadMedicalCenterSpecializationComponentsAction(Request $request)
    {
        $errors = array();
        $output = array();
        if ($request->isMethod('POST')) {

            $debugMode = isset($_GET['hcaDebug']) && $_GET['hcaDebug'] == 1;
            $institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($request->get('isId'));
            if (!$institutionSpecialization ) {
                throw $this->createNotFoundException('Invalid institution specialization');
            }
        
            $submittedSpecializations = $request->get(InstitutionSpecializationFormType::NAME);
            $em = $this->getDoctrine()->getEntityManager();
            foreach ($submittedSpecializations as $_isId => $_data) {
                if ($_isId == $institutionSpecialization->getSpecialization()->getId()) {
                    //delete treatments first
                    $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->deleteTreatmentsBySpecializationId($request->get('isId'));
                    // set passed treatments as choices
                    $default_choices = array();
                    $_treatment_choices = array();
                    if(!empty($_data['treatments'])){
                        $_treatment_choices = $this->get('services.treatment_bundle')->findTreatmentsByIds($_data['treatments']);
                        foreach ($_treatment_choices as $_t) {
                            $default_choices[$_t->getId()] = $_t->getName();
                            $institutionSpecialization->addTreatment($_t);
                        }
                        $form = $this->createForm('institutionSpecialization', $institutionSpecialization, array('default_choices' =>$default_choices ));
                        $form->bind($_data);
                        if ($form->isValid()) {
                            $em->persist($institutionSpecialization);
                            $em->flush();
                        
                            $output['html'] = $this->renderView('InstitutionBundle:MedicalCenter:list.treatments.html.twig', array(
                                'each' => array( 'treatments' => $_treatment_choices) ,
                            ));
                        }
                        else {
                            $errors[] = 'Failed form validation';
                        }
                    } else {
                        $errors = 'Please select at least one treatment';
                    }
                }
                $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
            }
            if (\count($errors) > 0) {
                $response = new Response($errors, 400);
            }
        }else{        
            $specializationTreatments = array();
            $institutionSpecializations = $this->institutionMedicalCenter->getInstitutionSpecializations();
            foreach ($institutionSpecializations as $e) {
                foreach ($e->getTreatments() as $t) {
                    $specializationTreatments[] = $t->getId();
                }
            }
            $form = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization());
            
            //TODO: this will pull in additional component data not needed by our view layer. create another method on service class.
            $specializationComponents = $this->get('services.treatment_bundle')->getTreatmentsBySpecializationIdGroupedBySubSpecialization($request->get('isId'));
            $html = $this->renderView('InstitutionBundle:Widgets/Profile:specializations.listForm.html.twig', array(
                            'specializationComponents' => $specializationComponents,
                            'specializationId' => $request->get('isId'),
                            'selectedTreatments' => $specializationTreatments,
                            'formName' => InstitutionSpecializationFormType::NAME,
                            'form' => $form->createView(),
            ));
            return new Response($html, 200);
        }
        return $response;
        
    }

    public function ajaxAddSpecializationAction(Request $request)
    {
        $specializations = $this->get('services.institution_specialization')->getNotSelectedSpecializations($this->institution);
        
        $params =  array(
            'specializations' => $specializations,
            'saveFormAction' => '',
            'buttonLabel' => ''
        );

        $html = $this->renderView('InstitutionBundle:Specialization/Widgets:form.multipleAdd.html.twig', $params);
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
    }
    
}