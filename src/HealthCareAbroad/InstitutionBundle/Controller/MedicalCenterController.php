<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionAffiliationsSelectorFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationSelectorFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionAffiliationFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

use HealthCareAbroad\InstitutionBundle\Repository\InstitutionMedicalCenterRepository;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for InstitutionMedicalCenter.
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class MedicalCenterController extends InstitutionAwareController
{
    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter = null;
    
    /**
     * @var InstitutionMedicalCenterRepository
     */
    private $repository;
    
    /**
     * @var InstitutionMedicalCenterService
     */
    private $service;
    
    public function preExecute()
    {
        $this->repository = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter');
        $this->service = $this->get('services.institution_medical_center');

                
        if ($imcId=$this->getRequest()->get('imcId',0)) {
            $this->institutionMedicalCenter = $this->repository->find($imcId);
            
            // non-existent medical center group
            if (!$this->institutionMedicalCenter) {
                if ($this->getRequest()->isXmlHttpRequest()) {
                    throw $this->createNotFoundException('Invalid medical center.');
                }
                else {
                    return $this->_redirectIndexWithFlashMessage('Invalid medical center.', 'error');
                }
            }

            // medical center group does not belong to this institution
            if ($this->institutionMedicalCenter->getInstitution()->getId() != $this->institution->getId()) {
                return $this->_redirectIndexWithFlashMessage('Invalid medical center.', 'error');
            }
        }
        

    }
    
    public function indexAction(Request $request)
    {
        return $this->render('InstitutionBundle:MedicalCenter:index.html.twig',array(
            'institution' => $this->institution,
            'medicalCenters' => $this->filteredResult
        ));
        
    }
    
    /**
     * Ajax handler for updating institution medical center by field
     * @param Request $request
     * @author acgvelarde
     */
    public function ajaxUpdateByFieldAction(Request $request)
    {
        $output = array();
        //if ($request->isMethod('POST')) {
        if (true) {
            try {
                $formVariables = $request->get(InstitutionMedicalCenterFormType::NAME);
                unset($formVariables['_token']);
                $removedFields = \array_diff(InstitutionMedicalCenterFormType::getFieldNames(), array_keys($formVariables));
                $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution),$this->institutionMedicalCenter, array(
                            InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => true,
                            InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => $removedFields
                        ));
                $form->bind($request);
                if ($form->isValid()) {
                    $this->institutionMedicalCenter = $form->getData();
                    $this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
                    
                    if ($this->institution->getType() == InstitutionTypes::SINGLE_CENTER) {
                        // also update the instituion name and description
                        $this->institution->setName($this->institutionMedicalCenter->getName());
                        $this->institution->setDescription($this->institutionMedicalCenter->getDescription());
                        $this->get('services.institution.factory')->save($this->institution);
                    }
                    
                    $output['institutionMedicalCenter'] = array();
                    foreach ($formVariables as $key => $v){
                        $output['institutionMedicalCenter'][$key] = $this->institutionMedicalCenter->{'get'.$key}();
                    }
                    
                    
                    $output['form_error'] = 0;
                }
                else {
                    // construct the error message
                    $html ="<ul class='errors'>";
                    foreach ($form->getErrors() as $err){
                        $html .= '<li>'.$err->getMessage().'</li>';
                    }
                    $html .= '</ul>';
                    $output['form_error'] = 1;
                    $output['form_error_html'] = $html;
                }
            }
            catch (\Exception $e) {
                return new Response($e->getMessage(),500);
            }
        }
        
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for loading tabbed contents of an institution medical center
     * @param Request $request
     */
    public function loadTabbedContentsAction(Request $request)
    {
   
        $content = $request->get('content');
        $output = array();
        $parameters = array('institutionMedicalCenter' => $this->institutionMedicalCenter);
        switch ($content) {
            case 'specializations':
                $parameters['specializations'] = $this->institutionMedicalCenter->getInstitutionSpecializations();
                $output['specializations'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterSpecializations.html.twig', $parameters));
                break;
            case 'services':
                $output['services'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterServices.html.twig'));
                break;
            case 'awards':
                $output['awards'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterAwards.html.twig'));
                break;
            case 'medical_specialists':
                $output['medical_specialists'] = array('html' => $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterSpecialists.html.twig'));
                break;
        }
        
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
    /**
     * @author Chaztine Blance
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMedicalCenterAction(Request $request)
    {
        $service = $this->get('services.institution_medical_center');
        
        if (is_null($this->institutionMedicalCenter)) {
            $this->institutionMedicalCenter = new institutionMedicalCenter();
            $this->institutionMedicalCenter->setInstitution($this->institution);
        }
    
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution),$this->institutionMedicalCenter);
         if ($request->isMethod('POST')) {
            $form->bind($request);
       
            // Get contactNumbers and convert to json format
            $businessHours = json_encode($request->get('businessHours'));
    
            if ($form->isValid()) {
           
                // Set BusinessHours before saving
                $form->getData()->setBusinessHours($businessHours);
                $form->getData()->setDescription($form->get('description')->getData());
                $form->getData()->setName($form->get('name')->getData());
                $form->getData()->setAddress($form->get('address')->getData());
                $this->institutionMedicalCenter = $service->saveAsDraft($form->getData());
                
                $this->institution->setContactNumber($form->get('contactNumber')->getData());
                $this->institution->setContactEmail($form->get('contactEmail')->getData());
                $institution = $this->get('services.institution.factory')->save($this->institution);
                
                
                //create event on editInstitution and dispatch
                $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $institution));
                
                // dispatch event
                $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER,
                                $this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER, $this->institutionMedicalCenter, array('institutionId' => $this->institution->getId())
                                ));
    
                $request->getSession()->setFlash('success', '"' . $this->institutionMedicalCenter->getName() . '"' . " has been created. You can now add Specializations to this center.");

//                 return $this->redirect($this->generateUrl('institution_medicalCenter_addSpecializations',array(
//                                 'institutionId' => $this->institution->getId(),
//                                 'imcId' => $this->institutionMedicalCenter->getId()
//                 )));
            }
        }
    
        $params = array(
                        'form' => $form->createView(),
                        'institution' => $this->institution,
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'selectedSubMenu' => 'centers'
        );
    
        return $this->render('InstitutionBundle:Institution:add.clinic.html.twig', $params);
    }
    
    
    /**
     * This is the first step when creating a new InstitutionMedicalCenter. Add details of a InstitutionMedicalCenter
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addDetailsAction(Request $request)
    {
        if (is_null($this->institutionMedicalCenter)) {
            $this->institutionMedicalCenter = new InstitutionMedicalCenter();
            $this->institutionMedicalCenter->setInstitution($this->institution);
        }
        else {
            // there is an imcId in the Request, check if this is a draft
            if ($this->institutionMedicalCenter && !$this->service->isDraft($this->institutionMedicalCenter)) {
                return $this->_redirectIndexWithFlashMessage('Invalid draft medical center', 'error');
            }
        }
        
        $form = $this->createForm(new InstitutionMedicalCenterFormType(),$this->institutionMedicalCenter);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                
                $this->institutionMedicalCenter = $this->get('services.institutionMedicalCenter')
                    ->saveAsDraft($form->getData());
                
                // TODO: fire event
                
                // redirect to step 2;
                return $this->redirect($this->generateUrl('institution_medicalCenter_addSpecializations',array('imcId' => $this->institutionMedicalCenter->getId())));
            }
        }
        
        return $this->render('InstitutionBundle:MedicalCenter:addDetails.html.twig', array('form' => $form->createView(), 'institutionMedicalCenter' => $this->institutionMedicalCenter));
    }
    
     /**
     * This is the second step when creating a center. This will add InstitutionMedicalCenter to the passed InstitutionMedicalCenter.
     * Expected GET parameters:
     *     - imcId institutionMedicalCenterId
     * 
     * @author Chaztine Blance
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addSpecializationsAction(Request $request)
    {
        $service = $this->get('services.institution_medical_center');

        if (!$this->institutionMedicalCenter) {
            throw $this->createNotFoundException('Invalid institutionMedicalCenter');
        }
        
        $specializations = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getActiveSpecializations();
        $form = $this->createForm(new InstitutionSpecializationSelectorFormType());
        $specializationArr = array();

        foreach ($specializations as $e) {
            $specializationArr[] = array('value' => $e->getName(), 'id' => $e->getId());
        }
        
        return $this->render('InstitutionBundle:MedicalCenter:addSpecializations.html.twig', array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institution' => $this->institution,
            'specializationsJSON' => \json_encode($specializationArr),
            'form' => $form->createView(),
            'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution)
        ));
    }
    
    public function saveSpecializationsAction(Request $request)
    {
        $submittedSpecializations = $request->get(InstitutionSpecializationFormType::NAME);
        $em = $this->getDoctrine()->getEntityManager();
        $errors = array();
        foreach ($submittedSpecializations as $specializationId => $_data) {
            $_institutionSpecialization = new InstitutionSpecialization();
            $_institutionSpecialization->setInstitutionMedicalCenter($this->institutionMedicalCenter);
            $_institutionSpecialization->setStatus(InstitutionSpecialization::STATUS_ACTIVE);
            $_institutionSpecialization->setDescription('');
            $form = $this->createForm(new InstitutionSpecializationFormType(), $_institutionSpecialization, array('em' => $em));
            $form->bind($_data);
            if ($form->isValid()) {
                $em->persist($form->getData());
                $em->flush();
            }
            else {
                
            }
        }
        
        if (\count($errors) > 0) {
            $request->getSession()->setFlash('notice', '<ul><li>'.\implode('</li><li>', $errors).'</li></ul>');
            $response = $this->redirect($this->generateUrl('institution_medicalCenter_addSpecializations', array('imcId' => $this->institutionMedicalCenter->getId())));
        }
        else {
            // redirect to next step
            $response = $this->redirect($this->generateUrl('institution_medicalCenter_addAncilliaryServices',  array('imcId' => $this->institutionMedicalCenter->getId())));
        }
        
        return $response;
    }
    
    public function addAncilliaryServicesAction(Request $request)
    {
        $form = $this->get('services.institution_medical_center_property.formFactory')->buildFormByInstitutionMedicalCenterPropertyTypeName($this->institution, $this->institutionMedicalCenter, 'ancilliary_service_id');
        $propertyService = $this->get('services.institution_medical_center_property');
        $medicalCenterService = $this->get('services.institution_medical_center');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
        
        if ($request->isMethod('POST')) {
            
            $form->bind($request);
            if ($form->isValid()) {
                $data = $form->getData();
                
                // clear first the existing ancilliary services of this medical center
                $medicalCenterService->clearPropertyValues($this->institutionMedicalCenter, $propertyType);
                
                // value is a doctrine collection of OfferService entity
                foreach ($data->getValue() as $_value) {
                    $_new = clone($data);
                    $_new->setValue($_value->getId());
                    $propertyService->save($_new);
                }
                
                
                return $this->redirect($this->generateUrl('institution_medicalCenter_addAffiliations', array('imcId' => $this->institutionMedicalCenter->getId())));
            }
            else {
                $request->getSession()->setFlash('notice', 'Please fill up form properly.');
            }
        }
        $selectedServices = array();
        foreach ($medicalCenterService->getPropertyValues($this->institutionMedicalCenter, $propertyType) as $_prop) {
            $selectedServices[] = $_prop->getValue();
        }
        
        return $this->render('InstitutionBundle:MedicalCenter:addAncilliaryService.html.twig', array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution),
            'form' => $form->createView(),
            'selectedServices' => $selectedServices
        ));
    }
    
    
    
    public function addDoctorsAction()
    {
        return $this->render('InstitutionBundle:MedicalCenter:addDoctors.html.twig', array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution),
            'currentDoctors' => $this->institutionMedicalCenter->getDoctors()
        ));
    }
    
    public function editAction(Request $request)
    {
        $institutionSpecializations = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getByInstitutionMedicalCenter($this->institutionMedicalCenter);
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter);
        $template = 'InstitutionBundle:MedicalCenter:view.html.twig';
        return $this->render($template, array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institutionSpecializations' => $institutionSpecializations,
            'institution' => $this->institution,
            'institutionMedicalCenterForm' => $form->createView()
        ));
    }
    
    public function viewAction(Request $request)
    {
    
    }
    
    public function saveAction(Request $request)
    {
    
    }
    
    /**
     * Ajax request handler for loading available specializations for an institution medical center group. 
     * This is used in the dropdown data for the Specialization field in add center form.
     * Current implementation implies that we can load all active Specializations, since an InstitutionMedicalCenter can have one or more InstitutionSpecializations 
     * 
     */
    public function loadAvailableSpecializationsAction()
    {
        // load all active medical centers
        $specializations = $this->get('services.specialization')->getAllActiveSpecializations();
        $html = '';
        foreach ($specializations as $each) {
            $html .= "<option value='{$each->getId()}'>{$each->getName()}</option>";
        }
        
        return new Response(\json_encode(array('html' => $html)),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for loading data 
     * Expected GET parameters
     *     - imcId instituitonMedicalCenterid
     *     - specializationId specializationId
     */
    public function loadAvailableTreatmentsAction(Request $request)
    {
        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->find($request->get('specializationId', 0));
        if (!$specialization) {
            throw $this->createNotFoundException("Invalid specialization");
        }
        
        // get all active Treatments under Specialization
        $treatments = $this->get('services.treatment')->getActiveTreatmentsBySpecialization($specialization);
        $html = '';
        
        if (count($treatments)) {
            $currentSubSpecialization = $treatments[0]->getSubSpecialization();
            $html .= "<optgroup label='{$currentSubSpecialization->getName()}'>";
            foreach ($treatments as $each) {
            
                if ($each->getTreatment()->getId() != $currentSubSpecialization->getId()) {
                    $currentSubSpecialization = $each->getSubSpecialization();
                    $html .= "</optgroup><optgroup label='{$currentSubSpecialization->getName()}'>";
                }
                $html .= "<option value='{$each->getId()}' style='margin-left:10px;'>{$each->getName()}</option>";
            }
            $html .= "</optgroup>";
        }
        
        
        return new Response(\json_encode(array('html' => $html)),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for searching available doctors for an InstitutionMedicalCenter
     * Expected GET parameters:
     *     - imcId institutionMedicalCenterId
     *     - searchKey
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchAvailableDoctorAction(Request $request)
    {
        $searchKey = \trim($request->get('searchKey',''));
        $availableDoctors = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')
            ->findAvailableDoctorBySearchKey($this->institutionMedicalCenter, $searchKey);
        
        $output = array();
        foreach ($availableDoctors as $doctor) {
            $arr = $this->get('services.doctor.twig.extension')->doctorToArray($doctor);
            $arr['html'] = $this->renderView('InstitutionBundle:MedicalCenter:doctorListItem.html.twig', array('imcId' => $this->institutionMedicalCenter->getId(),'doctor' => $doctor));
            $output[] = $arr;
        }
        
        //return $this->render('::base.ajaxDebugger.html.twig');
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for adding existing doctor to an InstitutionMedicalCenter
     * Expected parameters:
     *     - imcId institutionMedicalCenterId
     *     - doctorId
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addExistingDoctorAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId', 0));
        if (!$doctor) {
            throw $this->createNotFoundException('Invalid doctor.');
        }
        
        try{
            $this->institutionMedicalCenter->addDoctor($doctor);
            $this->service->save($this->institutionMedicalCenter);
        }
        catch (\Exception $e) {
                
        }
        
        return new Response(\json_encode(array()),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for removing a Doctor from InstitutionMedicalCenter
     * Expected parameters:
     *     - imcId
     *     - doctorId
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeDoctorAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId', 0));
        if (!$doctor) {
            throw $this->createNotFoundException('Invalid doctor.');
        }

        try{
            $this->institutionMedicalCenter->removeDoctor($doctor);
            $this->service->save($this->institutionMedicalCenter);
        }
        catch (\Exception $e) {
        
        }
        
        return new Response(\json_encode(array()),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Convenience function to redirect to medical center group index page with flash notice
     * 
     * @param string $flashMessage
     * @param string $type
     * @param string $redirectRoute
     */
    private function _redirectIndexWithFlashMessage($flashMessage, $type='success')
    {
        $this->getRequest()->getSession($type, $flashMessage);
        
        return $this->redirect($this->generateUrl('institution_medicalCenter_index'));
    }
    
    /**
     * @author Chaztine Blance
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * Adding of Insitution Affiliations
     */
    public function addAffiliationsAction(Request $request)
    {
        $form = $this->createForm(new InstitutionAffiliationsSelectorFormType());
        
        return $this->render('InstitutionBundle:MedicalCenter:addAffiliation.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution)
        ));
    }

}