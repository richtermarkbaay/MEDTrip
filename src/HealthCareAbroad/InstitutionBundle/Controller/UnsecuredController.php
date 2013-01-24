<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardsSelectorFormType;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionPropertyService;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * This controller will not go through security and login checks. 
 * Usually you will use this controller for non-secured transactions thru AJAX request.
 * Requests that will add/modify/delete data should not be put here.
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class UnsecuredController extends Controller
{
    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var Institution
     */
    private $institution;
    
    /**
     * @var InstitutionService
     */
    private $institutionService;
    
    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;
    
    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId'));
        $this->institutionService = $this->get('services.institution');
        
        if (!$this->institution) {
            throw $this->createNotFoundException("Invalid institution");
        }
        
        if ($imcId=$this->getRequest()->get('imcId',0)) {
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')
                ->findById($imcId);
            // institution medical center does not belong to institution
            if ($this->institutionMedicalCenter && $this->institutionMedicalCenter->getInstitution()->getId() != $this->institution->getId()) {
                
                return new Response('Medical center does not belong to institution', 401);
            }
        }
    }
    
    /**
     * Load tabbed contents of an institution
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadMultipleCenterInstitutionTabbedContentsAction()
    {
        $output = array();
        $parameters = array('institution' => $this->institution);
        $content = $this->request->get('content', null);
        switch ($content) {
            case 'services':
                $ancillaryServicesData = array(
                    'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                    'selectedAncillaryServices' => array()
                );
                
                foreach ($this->get('services.institution')->getInstitutionServices($this->institution) as $_selectedService) {
                    $ancillaryServicesData['selectedAncillaryServices'][] = $_selectedService['id'];
                }
                $parameters['services'] = $ancillaryServicesData;
                $output['services'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionServices.html.twig', $parameters));
                break;
            case 'awards':
                $form = $this->createForm(new InstitutionGlobalAwardsSelectorFormType());
                $currentGlobalAwards = $this->institutionService->getGroupedGlobalAwardsByType($this->institution);
                $autocompleteSource = $this->get('services.global_award')->getAutocompleteSource();
                
                $parameters['form'] = $form->createView();
                $parameters['isSingleCenter'] = $this->institutionService->isSingleCenter($this->institution);
                $parameters['awardsSourceJSON'] = \json_encode($autocompleteSource['award']);
                $parameters['certificatesSourceJSON'] = \json_encode($autocompleteSource['certificate']);
                $parameters['affiliationsSourceJSON'] = \json_encode($autocompleteSource['affiliation']);
                $parameters['accreditationsSourceJSON'] = \json_encode($autocompleteSource['accreditation']);
                $parameters['currentGlobalAwards'] = $currentGlobalAwards;
                $parameters['institution'] = $this->institution;
                $output['awards'] = array('html' => $this->renderView('InstitutionBundle:Institution/Widgets:institutionAwards.html.twig', $parameters));
        }
        
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Load tabbed contents of a medical center
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadMedicalCenterTabbedContentsAction()
    {
        if (!$this->institutionMedicalCenter) {
            throw $this->createNotFoundException("Invalid medical center");
        }
        
        $output = array();
        $content = $this->request->get('content', null);
        $institutionMedicalCenterService = $this->get('services.institution_medical_center');
        $parameters = array('institution' => $this->institution, 'institutionMedicalCenter' => $this->institutionMedicalCenter);
        $institutionMedicalCenterService = $this->get('services.institution_medical_center');
        $propertyService = $this->get('services.institution_medical_center_property');
        switch ($content) {
            case 'services':
                $ancillaryServicesData = array(
                    'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                    'selectedAncillaryServices' => array()
                );
        
                foreach ($institutionMedicalCenterService->getMedicalCenterServices($this->institutionMedicalCenter) as $_selectedService) {
                    $ancillaryServicesData['selectedAncillaryServices'][] = $_selectedService->getId();
                }
                $parameters['ancillaryServicesData'] = $ancillaryServicesData;
                $output['services'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterServices.html.twig', $parameters));
                break;
            case 'awards':
                $form = $this->createForm(new InstitutionGlobalAwardsSelectorFormType());
                $currentGlobalAwards = $institutionMedicalCenterService->getGroupedMedicalCenterGlobalAwards($this->institutionMedicalCenter);
                $autocompleteSource = $this->get('services.global_award')->getAutocompleteSource();
                 
                $parameters['form'] = $form->createView();
                $parameters['isSingleCenter'] = true;
                $parameters['awardsSourceJSON'] = \json_encode($autocompleteSource['award']);
                $parameters['certificatesSourceJSON'] = \json_encode($autocompleteSource['certificate']);
                $parameters['affiliationsSourceJSON'] = \json_encode($autocompleteSource['affiliation']);
                $parameters['currentGlobalAwards'] = $currentGlobalAwards;
                $parameters['accreditationsSourceJSON'] = \json_encode($autocompleteSource['accreditation']);
                $parameters['commonDeleteForm'] = $this->createForm(new CommonDeleteFormType())->createView();
                
                $output['awards'] = array('html' => $this->renderView('InstitutionBundle:MedicalCenter/Widgets:institutionMedicalCenterAwards.html.twig',$parameters));
                break;
            case 'medical_specialists':
                $doctors = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->getDoctorsByInstitutionMedicalCenter($this->institutionMedicalCenter->getId());
                $form = $this->createForm(new \HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType());
        
                if ($this->request->isMethod('POST')) {
                    $form->bind($this->request);
        
                    if ($form->isValid() && $form->get('id')->getData()) {
                        $center = $this->get('services.institution_medical_center')->saveInstitutionMedicalCenterDoctor($form->getData(), $this->institutionMedicalCenter);
                        $this->get('session')->setFlash('notice', "Successfully added Medical Specialist");
                    }
                }
                $doctorArr = array();
        
                foreach ($doctors as $each) {
                    $doctorArr[] = array('value' => $each['first_name'] ." ". $each['last_name'], 'id' => $each['id'], 'path' => $this->generateUrl('admin_doctor_load_doctor_specializations', array('doctorId' =>  $each['id'])));
                }
        
                $parameters['form'] = $form->createView();
                $parameters['doctorsJSON'] = \json_encode($doctorArr, JSON_HEX_APOS);
                $parameters['institution'] =  $this->institution;
                $parameters['doctors'] = $this->institutionMedicalCenter->getDoctors();
                $output['medical_specialists'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterSpecialists.html.twig',$parameters));
                break;
        }
        
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Load available global awards of an institution. Used in autocomplete fields
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGlobalAwardSourceAction(Request $request)
    {
        $term = \trim($request->get('term', ''));
        $type = $request->get('type', null);
        $types = \array_flip(GlobalAwardTypes::getTypeKeys());
        $type = \array_key_exists($type, $types) ? $types[$type] : 0;

        $output = array();
        $options = new QueryOptionBag();
        $options->add('globalAward.name', $term);
        if ($type) {
            $options->add('globalAward.type', $type);
        }


        $awards = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')
        ->getAvailableGlobalAwardsOfInstitution($this->institution, $options);


        foreach ($awards as $_award) {
            $output[] = array(
                'id' => $_award->getId(),
                'label' => $_award->getName(),
                'awardingBody' => $_award->getAwardingBody()->getName()
            );
        }
    
        return new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
    }
    
    /**
     * Remove global award of an institution medical center
     *
     * @param Request $request
     * @return \HealthCareAbroad\InstitutionBundle\Controller\Response
     */
    public function ajaxRemoveGlobalAwardAction(Request $request)
    {
        $award = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($request->get('id', 0));
    
        if (!$award) {
            throw $this->createNotFoundException();
        }
    
        $propertyService = $this->get('services.institution_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
    
        // get property value
        $property = $this->get('services.institution_medical_center')->getPropertyValue($this->institutionMedicalCenter, $propertyType, $award->getId());
    
        $form = $this->createForm(new CommonDeleteFormType(), $property);
    
        if ($request->isMethod('POST'))  {
            $form->bind($request);
            if ($form->isValid()) {
    
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($property);
                $em->flush();
    
                $response = new Response(\json_encode(array('id' => $award->getId())), 200, array('content-type' => 'application/json'));
            }
            else{
                $response = new Response("Invalid form", 400);
            }
        }
    
        return $response;
    }
}