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
        $this->institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($request->get('isId', 0));
        $treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($request->get('tId', 0));
    
        if (!$this->institutionSpecialization) {
            throw $this->createNotFoundException("Invalid institution specialization {$this->institutionSpecialization->getId()}.");
        }
        if (!$treatment) {
            throw $this->createNotFoundException("Invalid treatment {$treatment->getId()}.");
        }
        
        $form = $this->createForm(new CommonDeleteFormType(), $this->institutionSpecialization);
        
        if ($request->isMethod('POST'))  {
            $form->bind($request);
            if ($form->isValid()) {
                $this->institutionSpecialization->removeTreatment($treatment);
                $em = $this->getDoctrine()->getEntityManager();
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
}