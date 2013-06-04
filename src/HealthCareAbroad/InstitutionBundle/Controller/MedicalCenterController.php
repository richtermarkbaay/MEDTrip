<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterDoctorFormType;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use Mapping\Fixture\Xml\Status;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;
use HealthCareAbroad\TreatmentBundle\Entity\Specialization;
use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardsSelectorFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationSelectorFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;
use HealthCareAbroad\HelperBundle\Entity\GlobalAward;
use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use HealthCareAbroad\InstitutionBundle\Repository\InstitutionMedicalCenterRepository;
use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;
use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;
use HealthCareAbroad\MediaBundle\Services\MediaService;
use Gaufrette\File;

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
     * @var InstitutionSpecializations
     */
    private $institutionSpecializations = null;
    
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
        parent::preExecute();
        $this->repository = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter');
        $this->service = $this->get('services.institution_medical_center');

        if ($imcId=$this->getRequest()->get('imcId',0)) {
            $this->institutionMedicalCenter = $this->service->findById($imcId);
            
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
    
    /**
     * View all medical centers of current institution
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // Medical Centers Group By Status
        $medicalCenters = $this->service->groupMedicalCentersByStatus($this->institution->getInstitutionMedicalCenters());
//         var_dump($medicalCenters[InstitutionMedicalCenterStatus::ARCHIVED]);exit;
        
        // Add Medical Center Form
        $institutionMedicalCenter = new InstitutionMedicalCenter();
        $institutionMedicalCenter->setInstitution($this->institution);
        $phoneNumber = new ContactDetail();
        $phoneNumber->setType(ContactDetailTypes::PHONE);
        $institutionMedicalCenter->addContactDetail($phoneNumber);
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false));

        $parameters = array(
            'institution' => $this->institution,
            'institutionMedicalCenterForm' => $form->createView(),
            'approvedMedicalCenters' => $medicalCenters[InstitutionMedicalCenterStatus::APPROVED],
            'draftMedicalCenters' => $medicalCenters[InstitutionMedicalCenterStatus::DRAFT],
            'pendingMedicalCenters' => $medicalCenters[InstitutionMedicalCenterStatus::PENDING],
            'expiredMedicalCenters' => $medicalCenters[InstitutionMedicalCenterStatus::EXPIRED],
            'archivedMedicalCenters' => $medicalCenters[InstitutionMedicalCenterStatus::ARCHIVED],
            'isInquiry' => true
        );
        
        return $this->render('InstitutionBundle:MedicalCenter:index.html.twig', $parameters);
    }
    
    
    /**
     * Profile page of a medical center
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request)
    {
        if(!$this->institutionMedicalCenter->getContactDetails()->count()) {
            $contactDetails = new ContactDetail();
            $contactDetails->setType(ContactDetailTypes::PHONE);
            $this->institutionMedicalCenter->addContactDetail($contactDetails);
        }
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false));
        $template = 'InstitutionBundle:MedicalCenter:view.html.twig';
        $institutionSpecializations = $this->institutionMedicalCenter->getInstitutionSpecializations();
        $specializations = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getActiveSpecializations();
        $currentGlobalAwards = $this->get('services.institution_medical_center_property')->getGlobalAwardPropertiesByInstitutionMedicalCenter($this->institutionMedicalCenter);
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
        
        return $this->render('InstitutionBundle:MedicalCenter:view.html.twig', array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'specializations' => $institutionSpecializations,
            'institution' => $this->institution,
            'ancillaryServicesData' =>  $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
            'institutionMedicalCenterForm' => $form->createView(),
            'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView(),
            'currentGlobalAwards' => $currentGlobalAwards,
            'editGlobalAwardForm' => $editGlobalAwardForm->createView()
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
        $propertyService = $this->get('services.institution_medical_center_property');
        if (true) {
            try {
                $formVariables = $request->get(InstitutionMedicalCenterFormType::NAME);
                unset($formVariables['_token']);
                $removedFields = \array_diff(InstitutionMedicalCenterFormType::getFieldNames(), array_keys($formVariables));
                
                if(!$this->institutionMedicalCenter->getContactDetails()->count()) {
                    $phoneNumber = new ContactDetail();
                    $phoneNumber->setType(ContactDetailTypes::PHONE);
                    $phoneNumber->setNumber('');
                    $this->institutionMedicalCenter->addContactDetail($phoneNumber);
                }
                
                $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution),$this->institutionMedicalCenter, array(
                    InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false,
                    InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => $removedFields
                ));
                $form->bind($request);
                if ($form->isValid()) {
                    $this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
                    
                    if(!empty($form['services']))
                    {
                        $propertyService->removeInstitutionMedicalCenterPropertiesByPropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE, $this->institutionMedicalCenter);
                        $propertyService->addPropertyForInstitutionMedicalCenterByType($this->institution, $form['services']->getData(),InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE, $this->institutionMedicalCenter);

                    }if(!empty($form['awards']))
                    {
//                         $propertyService->removeInstitutionMedicalCenterPropertiesByPropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD, $this->institutionMedicalCenter);
                        $propertyService->addPropertyForInstitutionMedicalCenterByType($this->institution, $form['awards']->getData(),InstitutionPropertyType::TYPE_GLOBAL_AWARD, $this->institutionMedicalCenter);
                    }
                    
                    if ($this->institution->getType() == InstitutionTypes::SINGLE_CENTER) {
                        // also update the instituion name and description
                        $this->institution->setName($this->institutionMedicalCenter->getName());
                        $this->institution->setDescription($this->institutionMedicalCenter->getDescription());
                        $this->get('services.institution.factory')->save($this->institution);
                    }
                    
                    $output['institutionMedicalCenter'] = array();
                    foreach ($formVariables as $key => $v){
                        
                        if($key == 'services')
                        {
                            $html = $this->renderView('InstitutionBundle:Widgets:tabbedContent.institutionMedicalCenterServices.html.twig', array(
                                    'institution' => $this->institution,
                                    'institutionMedicalCenter' => $this->institutionMedicalCenter,
                                    'ancillaryServicesData' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                            ));
                        
                            return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
                        
                        }if($key == 'awards')
                        {
                            $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
                            $html = $this->renderView('InstitutionBundle:MedicalCenter/Widgets:institutionMedicalCenterAwards.html.twig', array(
                                    'institution' => $this->institution,
                                    'institutionMedicalCenter' => $this->institutionMedicalCenter,
                                    'editGlobalAwardForm' => $editGlobalAwardForm->createView(),
                                    'currentGlobalAwards' => $propertyService->getGlobalAwardPropertiesByInstitutionMedicalCenter($this->institutionMedicalCenter),
                            ));
                        
                            return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
                        }     
                         if($key == 'contactDetails' ){
                            $value = $this->institutionMedicalCenter->{'get'.$key}();
                            $returnVal = array();
                                foreach ($value as $keys => $a){
                                   if($a->getType() == ContactDetailTypes::MOBILE){
                                       $returnVal['mobileNumber'] = $a->getNumber();
                                   }else{
                                       $returnVal['phoneNumber'] =  $a->getNumber();
                                   }
                                }    
                               
                            $output['institutionMedicalCenter'][$key] = $returnVal;
                        }
                        else{
                            
                            $value = $this->institutionMedicalCenter->{'get'.$key}();
                            if($key == 'address') {
                                $value = json_decode($value, true);
                                $output['institutionMedicalCenter']['country']= $this->institution->getCountry()->getName();
                                $output['institutionMedicalCenter']['city'] = $this->institution->getCity()->getName();
                                $output['institutionMedicalCenter']['state'] = $this->institution->getState();
                                $output['institutionMedicalCenter']['zipCode'] = $this->institution->getZipCode();
                            }
                            
                            if($key == 'contactNumber' || $key == 'socialMediaSites') {
                                $value = json_decode($value, true);
                            }
                            $output['institutionMedicalCenter'][$key] = $value;
                        }
                    }

                    $output['form_error'] = 0;
//                     $output['calloutView'] = $this->_getEditMedicalCenterCalloutView();
                    $response = new Response(\json_encode($output),200, array('content-type' => 'application/json'));
                }
                 else {
                    $errors = array();
                    $form_errors = $this->get('validator')->validate($form);
                     
                    foreach ($form_errors as $_err) {
                        $errors[] = array('field' => str_replace('data.','',$_err->getPropertyPath()), 'error' => $_err->getMessage());
                    }
                    return new Response(\json_encode(array('html' => $errors)), 400, array('content-type' => 'application/json'));
                }
            }
            catch (\Exception $e) {
                return new Response($e->getMessage(),500);
            }
        }
        
        return $response;
    }
    
    /**
     * Ajax handler for updating institution coordinates field.
     *
     * @param Request $request
     * @author Adelbert D. Silla
     */
    public function ajaxUpdateCoordinatesAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->institutionMedicalCenter->setCoordinates($request->get('coordinates'));
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($this->institutionMedicalCenter);
            $em->flush($this->institutionMedicalCenter);

            return new Response(\json_encode(true),200, array('content-type' => 'application/json'));
        }
    }
    
    /**
     * Save clinic business hours
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author acgvelarde, alnie
     */
    public function ajaxUpdateBusinessHoursAction(Request $request)
    {
        $defaultDailyData = array('isOpen' => 0, 'notes' => '');
        $businessHours = $request->get('businessHours', array());
        foreach ($businessHours as $_day => $data) {
            $businessHours[$_day] = \array_merge($defaultDailyData, $data);
        }
        
        $jsonEncodedBusinessHours = InstitutionMedicalCenterService::jsonEncodeBusinessHours($businessHours);
        $this->institutionMedicalCenter->setBusinessHours($jsonEncodedBusinessHours);
        try {
            $this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
            $html = $this->renderView('InstitutionBundle:MedicalCenter/Widgets:businessHoursTable.html.twig', array('institutionMedicalCenter' => $this->institutionMedicalCenter));

            $responseContent = array('html' => $html, 'calloutView' => $this->_getEditMedicalCenterCalloutView());
            $response = new Response(\json_encode($responseContent), 200, array('content-type' => 'application/json'));
        }
        catch (\Exception $e) {
            $response = new Response($e->getMessage(), 500);
        }
        
        return $response;
    }
    
    /** edited for newly markup
     * Add new CLINIC CENTER
     * @author Chaztine Blance
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMedicalCenterAction(Request $request)
    {
        if ($request->isMethod('POST')) {
     
            if (!$this->institutionMedicalCenter instanceof InstitutionMedicalCenter) {
                $this->institutionMedicalCenter = new InstitutionMedicalCenter();
                $this->institutionMedicalCenter->setInstitution($this->institution);
            }
            
            $formVariables = $request->get(InstitutionMedicalCenterFormType::NAME);
            unset($formVariables['_token']);
            $removedFields = \array_diff(InstitutionMedicalCenterFormType::getFieldNames(), array_keys($formVariables));
            
            if(!$this->institutionMedicalCenter->getContactDetails()->count()) {
                $phoneNumber = new ContactDetail();
                $phoneNumber->setType(ContactDetailTypes::PHONE);
                $this->institutionMedicalCenter->addContactDetail($phoneNumber);
            }
            
            $this->institutionMedicalCenter->setDescription(' ');
            $this->institutionMedicalCenter->setAddress($this->institution->getAddress1());
            $this->institutionMedicalCenter->setAddressHint($this->institution->getAddressHint());
            $this->institutionMedicalCenter->setCoordinates($this->institution->getCoordinates());
            
            $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution),$this->institutionMedicalCenter, array(
                InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false,
                InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => $removedFields
            ));
            
            $form->bind($request);

            if ($form->isValid()) {
                $this->institutionMedicalCenter = $this->get('services.institutionMedicalCenter')->saveAsDraft($form->getData());
                $output =  $this->generateUrl('institution_medicalCenter_view', array('imcId' => $this->institutionMedicalCenter->getId()));
                
                $response = new Response(\json_encode(array('redirect' => $output)), 200, array('content-type' => 'application/json'));
            }      
            else {
                $errors = array();
                $form_errors = $this->get('validator')->validate($form);
                 
                foreach ($form_errors as $_err) {
                    $errors[] = array('field' => str_replace('data.','',$_err->getPropertyPath()), 'error' => $_err->getMessage());
                }
                $response = new Response(\json_encode(array('html' => $errors)), 400, array('content-type' => 'application/json'));
            }
            
        }
        return $response;
    }
         
    /*
     * 
     * This is the last step in creating a center. This will add medicalSpecialist on InstitutionMedicalCenter
     */
    public function addMedicalSpecialistAction(Request $request)
    {
        
        $isSingleCenter = $this->get('services.institution')->isSingleCenter($this->institution);
        $doctors = $this->institutionMedicalCenter->getDoctors();//$this->getDoctrine()->getRepository('DoctorBundle:Doctor')->getDoctorsByInstitutionMedicalCenter($this->institutionMedicalCenter->getId());
        $form = $this->createForm(new \HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType());

        if ($request->isMethod('POST')) {
            
            $form->bind($request);
            if ($form->isValid()) {
                $params = array();
                if ($isSingleCenter) {
                    // Update SignupStepStatus
                    // TODO: @deprecated
                } else {
                    $calloutParams = array(
                        '{CENTER_NAME}' => $this->institutionMedicalCenter->getName(),
                        '{ADD_CLINIC_URL}' => $this->generateUrl('institution_medicalCenter_add')
                    );
                    $calloutMessage = $this->get('services.institution.callouts')->get('success_add_center', $calloutParams);
                    $this->getRequest()->getSession()->getFlashBag()->add('callout_message', $calloutMessage);
                    
                    $params['imcId'] = $this->institutionMedicalCenter->getId();
                    
                }

                return $this->redirect($this->generateUrl($routeName, $params));
            }
        }

        $doctorArr = array();
        foreach ($doctors as $each) {
           $doctorArr[] = array('value' => $each->getFirstName() ." ". $each->getLastName(), 'id' => $each->getId());
        }

        return $this->render('InstitutionBundle:MedicalCenter:add.medicalSpecialist.html.twig', array(
            'form' => $form->createView(),
            'isNoBreadCrumbs' => true,
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'isSingleCenter' => $isSingleCenter,
            'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView(),
            'doctors' => $doctors//\json_encode($doctorArr, JSON_HEX_APOS)
        ));
    }
    
    /**
     * TODO: Separate logic for AJAX request.
     * @param Request $request
     * @return Ambigous <\Symfony\Component\HttpFoundation\Response, \Symfony\Component\HttpFoundation\RedirectResponse>
     */
    public function saveSpecializationsAction(Request $request)
    {
        $submittedSpecializations = $request->get(InstitutionSpecializationFormType::NAME);
        $em = $this->getDoctrine()->getEntityManager();
        $ajaxOutput = array('html' => '');
        $errors = array();
        $commonDeleteForm = $this->createForm(new CommonDeleteFormType()); // used only in ajax request
        if (\count($submittedSpecializations) > 0) {
            
            foreach ($submittedSpecializations as $specializationId => $_data) {

                $specialization = $this->get('services.treatment_bundle')->getSpecialization($specializationId);
                $_institutionSpecialization = new InstitutionSpecialization();
                $_institutionSpecialization->setSpecialization($specialization);
                $_institutionSpecialization->setInstitutionMedicalCenter($this->institutionMedicalCenter);
                $_institutionSpecialization->setStatus(InstitutionSpecialization::STATUS_ACTIVE);
                $_institutionSpecialization->setDescription('');
                
                // set passed treatments as choices
                $default_choices = array();
                if(!empty($_data['treatments'])){
                    $_treatment_choices = $this->get('services.treatment_bundle')->findTreatmentsByIds($_data['treatments']);
                        foreach ($_treatment_choices as $_t) {
                            $default_choices[$_t->getId()] = $_t->getName();
                            // add the treatment
                            $_institutionSpecialization->addTreatment($_t);
                        }
                }
                $form = $this->createForm(new InstitutionSpecializationFormType(), $_institutionSpecialization, array('default_choices' => $default_choices));
                $form->bind($_data);
                if ($form->isValid()) {
                    $em->persist($_institutionSpecialization);
                    $em->flush();
                    
                    if ($request->isXmlHttpRequest()) {
                        $ajaxOutput['html'] = $this->renderView('InstitutionBundle:MedicalCenter:listItem.institutionSpecializationTreatments.html.twig', array(
                            'each' => $_institutionSpecialization,
                            'institutionMedicalCenter' => $this->institutionMedicalCenter,
                            'commonDeleteForm' => $commonDeleteForm->createView()
                        ));
                    }
                }
                else {
                    foreach ($form->getErrors() as $_error) {
                        $errors[] = $_error->getMessage();
                    }
                }
            }    
        }
        else {
            $errors[] = 'Please provide at least one specialization.';
        }
        
        // if AJAX request
        if ($request->isXmlHttpRequest()) {
            if (\count($errors) > 0) {
                $response = new Response('<ul><li>'.\implode('</li><li>', $errors).'</li></ul>',400);
            }
            else {
                $ajaxOutput['calloutView'] = $this->_getEditMedicalCenterCalloutView();
                $response = new Response(\json_encode($ajaxOutput),200, array('content-type' => 'application/json'));
            }
        }
        return $response;
    }
    
    public function addDoctorsAction()
    {
        $doctors = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->findAll();
        $form = $this->createForm(new \HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType());
        $doctorArr = array();
        foreach ($doctors as $e) {
            $doctorArr[] = array('value' => $e->getFirstName() ." ". $e->getLastName(), 'id' => $e->getId());
        }
        
        return $this->render('InstitutionBundle:MedicalCenter:addDoctors.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution),
            'doctorsJSON' => \json_encode($doctorArr, JSON_HEX_APOS),
            'currentDoctors' => $this->institutionMedicalCenter->getDoctors()
        ));
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
     * Ajax handler for searching available doctors for an InstitutionMedicalCenter in Client-Admin
     * Expected GET parameters:
     *     - imcId institutionMedicalCenterId
     *     - searchKey
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loadMedicalSpecialistAction(Request $request)
    {
        $doctors = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getAvailableDoctorsByInstitutionMedicalCenter($this->institutionMedicalCenter, \trim($request->get('term','')));
        $doctorArr = array();
        foreach ($doctors as $each) {
            $doctorArr[] = array('value' => $each['first_name'] ." ". $each['last_name'], 'id' => $each['id'], 'path' => $this->generateUrl('admin_doctor_specializations', array('doctorId' =>  $each['id'])));
        }
        
        return new Response(\json_encode($doctorArr, JSON_HEX_APOS), 200, array('content-type' => 'application/json'));
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
        $result = array('status' => false);
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId', 0));
        if (!$doctor) {
            $result['message'] = 'Invalid doctor.';
        }
        try{
            $this->institutionMedicalCenter->addDoctor($doctor);
            $this->service->save($this->institutionMedicalCenter);
            $result['status'] = true;
            $result['doctor'] = $this->get('services.doctor')->toArrayDoctor($doctor);
        }
        catch (\Exception $e) {}

        return new Response(\json_encode($result),200, array('content-type' => 'application/json'));
    }
    
    /**
     * Ajax handler for update doctor to an InstitutionMedicalCenter
     * Expected parameters:
     *     - imcId institutionMedicalCenterId
     *     - doctorId
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxUpdateDoctorAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId'));

        if(!$doctor->getContactDetails()->count()) {
            $number = new ContactDetail();
            $number->setType(ContactDetailTypes::PHONE);
            $doctor->addContactDetail($number);
        }
 
         
        $form = $this->createForm(new InstitutionMedicalCenterDoctorFormType('editInstitutionMedicalCenterDoctorForm'), $doctor);
        $form->bind($request);

        if(!$doctor->getContactDetails()->first()->getNumber()) {
            $doctor->getContactDetails()->remove(0);
        }
        
        if ($form->isValid()) {
            $fileBag = $request->files->get($form->getName()); 

            if(isset($fileBag['media'])) {
                $this->get('services.doctor.media')->uploadLogo($fileBag['media'], $doctor);
            }

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($doctor);
            $em->flush();

            $data = array(
                'status' => true,
                'message' => 'Doctor has been added to your clinic!',
                'doctor' => $this->get('services.doctor')->toArrayDoctor($doctor)
            );
        } else {
            $data = array('status' => false, 'message' => $form->getErrorsAsString());
        }

        $request->getSession()->setFlash('notice', 'Doctor has been updated.');

        return $this->redirect($request->headers->get('referer'));
        
        //return new Response(\json_encode($result),200, array('content-type' => 'application/json'));
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
        $result = array('status' => false);
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId', 0));
        if (!$doctor) {
            $result['message'] = 'Invalid Doctor Id.';
        }

        try{
            $this->institutionMedicalCenter->removeDoctor($doctor);
            $this->service->save($this->institutionMedicalCenter);
            $result['status'] = true;
            $result['message'] = 'Doctor successfully removed from your clinic.';
        }
        catch (\Exception $e) {}

        return new Response(\json_encode($result),200, array('content-type' => 'application/json'));
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
     * Adding of Insitution GlobalAwards
     */
    public function addGlobalAwardsAction(Request $request)
    {
        $form = $this->createForm(new InstitutionGlobalAwardsSelectorFormType());
        $currentGlobalAwards = $this->get('services.institution_medical_center')->getGroupedMedicalCenterGlobalAwards($this->institutionMedicalCenter);
        $autocompleteSource = $this->get('services.global_award')->getAutocompleteSource();
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
        return $this->render('InstitutionBundle:MedicalCenter:addGlobalAward.html.twig', array(
            'form' => $form->createView(),
            'editGlobalAwardForm' => $editGlobalAwardForm->createView(),
            'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution),
            'awardsSourceJSON' => \json_encode($autocompleteSource['award']),
            'certificatesSourceJSON' => \json_encode($autocompleteSource['certificate']),
            'affiliationsSourceJSON' => \json_encode($autocompleteSource['affiliation']),
            'currentGlobalAwards' => $currentGlobalAwards
        ));
    }
    
    public function ajaxRemovePropertyValueAction(Request $request)
    {
        $property = $this->get('services.institution_medical_center_property')->findById($request->get('id', 0));
        
        if (!$property) {
            throw $this->createNotFoundException('Invalid medical center property.');
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($property);
        $em->flush();
        
        return new Response("Property removed", 200);
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
                
                $responseContent = array('id' => $_id, 'calloutView' => $this->_getEditMedicalCenterCalloutView());
                $response = new Response(\json_encode($responseContent), 200, array('content-type' => 'application/json'));
            }
            else {
                $response = new Response("Invalid form", 400);
            }
        }
//         else {
            
//             $html = $this->renderView('InstitutionBundle:Widgets:modal.deleteSpecialization.html.twig', array(
//                 'institutionMedicalCenter' => $this->institutionMedicalCenter,
//                 'institutionSpecialization' => $institutionSpecialization,
//                 'form' => $form->createView()
//             ));
            
//             $response = new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
//         }
        
        return $response;
    }
    
    /**
     * Remove an ancillary service to medical center
     * Required parameters:
     *     - institutionId
     *     - imcId institution medical center id
     *     - asId ancillary service id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxRemoveAncillaryServiceAction(Request $request)
    {
        $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->find($request->get('id', 0));
        
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')->find($property->getValue());
        
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($property);
            $em->flush();
        
            $output = array(
                    'label' => 'Add Service',
                    'href' => $this->generateUrl('institution_medicalCenter_ajaxAddAncillaryService', array('institutionId' => $this->institution->getId(),'imcId' => $this->institutionMedicalCenter->getId() ,'id' => $ancillaryService->getId() )),
                    '_isSelected' => false,
            );
        
            $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
        }
        
        catch (\Exception $e){
            $response = new Response($e->getMessage(), 500);
        }
        
        return $response;
    }
    
    /**
     * Add an ancillary service to medical center
     * Required parameters:
     *     - institutionId
     *     - imcId institution medical center id
     *     - asId ancillary service id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxAddAncillaryServiceAction(Request $request)
    {
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')->find($request->get('id', 0));
    
        if (!$ancillaryService) {
            throw $this->createNotFoundException('Invalid ancillary service id');
        }
    
        $propertyService = $this->get('services.institution_medical_center_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
    
        // check if this medical center already have this property value
        if ($this->get('services.institution_medical_center')->hasPropertyValue($this->institutionMedicalCenter, $propertyType, $ancillaryService->getId())) {
            $response = new Response("Property value {$ancillaryService->getId()} already exists.", 500);
        }
        else {
            $property = $propertyService->createInstitutionMedicalCenterPropertyByName($propertyType->getName(), $this->institution, $this->institutionMedicalCenter);
            $property->setValue($ancillaryService->getId());
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($property);
                $em->flush();
    
                $output = array(
                                'label' => 'Delete Service',
                                'href' => $this->generateUrl('institution_medicalCenter_ajaxRemoveAncillaryService', array('institutionId' => $this->institution->getId(),'imcId' => $this->institutionMedicalCenter->getId() ,'id' => $property->getId() )),
                                '_isSelected' => true,
                                'calloutView' => $this->_getEditMedicalCenterCalloutView()
                );
                
                $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
            }
            catch (\Exception $e){
                $response = new Response($e->getMessage(), 500);
            }
        }
    
        return $response;
    }
    
    /**
     * Save Specialization treatments under clinic profile page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Chaztine Blance
     */
    public function ajaxAddInstitutionSpecializationTreatmentsAction(Request $request)
    {
        $debugMode = isset($_GET['hcaDebug']) && $_GET['hcaDebug'] == 1;
        $institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($request->get('isId'));
        if (!$institutionSpecialization ) {
            throw $this->createNotFoundException('Invalid institution specialization');
        }
        
        if ($request->isMethod('POST')) {
            $submittedSpecializations = $request->get(InstitutionSpecializationFormType::NAME);
            
            $em = $this->getDoctrine()->getEntityManager();
            $errors = array();
            $output = array('html' => '');
            foreach ($submittedSpecializations as $_isId => $_data) {
                if ($_isId == $institutionSpecialization->getSpecialization()->getId()) {
                    
                    // set passed treatments as choices
                    $default_choices = array();
                    $_treatment_choices = $this->get('services.treatment_bundle')->findTreatmentsByIds($_data['treatments']);
                    foreach ($_treatment_choices as $_t) {
                        $default_choices[$_t->getId()] = $_t->getName();
                        // add the treatment
                        $institutionSpecialization->addTreatment($_t);
                    }
                    
                    $form = $this->createForm('institutionSpecialization', $institutionSpecialization, array('default_choices' =>$default_choices ));
                    $form->bind($_data);
                    if ($form->isValid()) {
                        try {
                            
                            //$institutionSpecialization = $form->getData();
                            $em->persist($institutionSpecialization);
                            $em->flush();
                            
                            $output['html'] = $this->renderView('InstitutionBundle:MedicalCenter:list.treatments.html.twig', array(
                                'institutionSpecialization' => $institutionSpecialization,
                                'institutionMedicalCenter' => $this->institutionMedicalCenter,
                                'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView()
                            ));

                        }catch (\Exception $e) {
                            $errors[] = $e->getMessage();
                        }
                    }
                    else {
                        var_dump($form->getErrorsAsString()); exit;
                        $errors[] = 'Failed form validation';
                    }
                }
            }
            
            if (\count($errors) > 0) {
                $response = new Response('Errors: '.implode('\n',$errors), 400);
            }
            else {
                $output['calloutView'] = $this->_getEditMedicalCenterCalloutView();
                $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
            }
        }
        
        return $response;   
    }
    
    public function ajaxLoadSpecializationAccordionEntryAction(Request $request)
    {
        $specializationId = $request->get('specializationId', 0);
        
        $criteria = array('status' => Specialization::STATUS_ACTIVE, 'id' => $specializationId);
        
        $params['specialization'] = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findOneBy($criteria);
        
        if(!$params['specialization']) {
            $result = array('error' => 'Invalid Specialization');
        
            return new Response('Invalid Specialization', 404);
        }
        
        $groupBySubSpecialization = true;
        $form = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization());
        $params['formName'] = InstitutionSpecializationFormType::NAME;
        $params['form'] = $form->createView();
        $params['subSpecializations'] = $this->get('services.treatment_bundle')->getTreatmentsBySpecializationGroupedBySubSpecialization($params['specialization']);
        $params['showCloseBtn'] = $this->getRequest()->get('showCloseBtn', true);
        $params['selectedTreatments'] = $this->getRequest()->get('selectedTreatments', array());
        $params['treatmentsListOnly'] = (bool)$this->getRequest()->get('treatmentsListOnly', 0);
        
        $html = $this->renderView('InstitutionBundle:MedicalCenter:specializationAccordion.html.twig', $params);
        //         $html = $this->renderView('HelperBundle:Widgets:testForm.html.twig', $params);
        
        return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
    }
    
    /**
     * Add an medical specialist to medical center
     * Required parameters:
     *     - institutionId
     *     - imcId institution medical center id
     *     - docorId doctor id
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author Chaztine Blance
     */
    public function ajaxAddSpecialistAction(Request $request)
    {
        $specialist = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('id'));
        
        if (!$specialist) {
            throw $this->createNotFoundException();
        }
        // check if this medical center already have this property
        if ($this->get('services.institution_medical_center')->hasSpecialist($this->institutionMedicalCenter, $request->get('id'))) {
            $response = new Response("Medical specialist value {$specialist->getId()} already exists.", 500);
        }
        else {
            $this->institutionMedicalCenter->addDoctor($specialist);
            $this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
            
            $html = $this->renderView('InstitutionBundle:MedicalCenter:tableRow.specialist.html.twig', array('doctors' => array($specialist) , 'institutionMedicalCenter' => $this->institutionMedicalCenter));
            $calloutView = $this->_getEditMedicalCenterCalloutView();
            $response = new Response(\json_encode(array('html' => $html, 'calloutView' => $calloutView)), 200, array('content-type' => 'application/json'));
        }
        
        return $response;
    }
    
    public function ajaxRemoveSpecialistAction(Request $request)
    {
       $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('id', 0));

        if (!$doctor) {
            
            throw $this->createNotFoundException('Invalid medical center property.');
        }
        
        $form = $this->createForm(new CommonDeleteFormType(), $doctor);
        
        if ($request->isMethod('POST'))  {
            $form->bind($request);
            if ($form->isValid()) {
                
                $this->institutionMedicalCenter->removeDoctor($doctor);
                $this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
                $calloutView = $this->_getEditMedicalCenterCalloutView();

                $response = new Response(\json_encode(array('id' => $doctor->getId(), 'calloutView' => $calloutView)), 200, array('content-type' => 'application/json'));
            }
            else{
                $response = new Response("Invalid form", 400);
            }
        }
        
        return $response;
    }

    /**
     * Upload logo for Institution Medical Center
     * @param Request $request
     */
    public function uploadAction(Request $request)
    {
        if ($request->files->get('logo')) {
            $file = $request->files->get('logo');
            $this->get('services.institution.media')->medicalCenterUploadLogo($file, $this->institutionMedicalCenter);
        }

        return $this->redirect($this->getRequest()->headers->get('referer'));
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