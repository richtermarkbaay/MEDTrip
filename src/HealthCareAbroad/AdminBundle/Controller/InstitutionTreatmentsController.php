<?php
/**
 *
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\HelperBundle\Entity\GlobalAward;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\InstitutionGlobalAwardExtraValueDataTransformer;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardsSelectorFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\DoctorBundle\Form\DoctorFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationSelectorFormType;

use HealthCareAbroad\MediaBundle\Services\MediaService;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;

use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterBusinessHourFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * Controller for handling actions related to InstitutionMedicalCenter and treatments
 *
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionTreatmentsController extends Controller
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
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;

    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId', 0));
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
        }

        // check InstitutionMedicalCenter
        if ($institutionMedicalCenterId = $this->request->get('imcId', 0)) {
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($institutionMedicalCenterId);
            if (!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid institution medical center');
            }

        }
    }


    public function viewAllMedicalCentersAction()
    {
        $institutionService = $this->get('services.institution');
        if($institutionService->isSingleCenter($this->institution)) {
            $firstMedicalCenter = $institutionService->getFirstMedicalCenter($this->institution);
            if ($firstMedicalCenter) {
                // forward action to viewing a medical center
                $response = $this->forward('AdminBundle:InstitutionTreatments:viewMedicalCenter',array('institution' => $this->institution, 'center' => $firstMedicalCenter, 'isOpen24hrs' => $this->get('services.institution_medical_center')->checkIfOpenTwentyFourHours(\json_decode($firstMedicalCenter->getBusinessHours(),true))));
            }
            else {
                // no medical center yet, redirect to add medical center with 
                $this->request->getSession()->setFlash('notice', 'No medical centers yet, add a clinic now.');
                $response = $this->redirect($this->generateUrl('admin_institution_medicalCenter_add', array('institutionId' => $this->institution->getId())));
            }
                
        }
        else {
            $institutionMedicalCenters = $institutionService->getAllMedicalCenters($this->institution);
            
            // get global ancillary services
            $ancillaryServicesData = array(
                'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                'selectedAncillaryServices' => array()
            );
            
            $params = array(
                'institution' => $this->institution,
                'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
                'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions(),
                'institutionMedicalCenters' => $institutionMedicalCenters,
                'institutionSpecializationsData' => array(),
                'ancillaryServicesData' => $ancillaryServicesData,
                'pager' => $this->pager,
                'isSingleCenter' => false
            );
            
            $response = $this->render('AdminBundle:InstitutionTreatments:viewAllMedicalCenters.html.twig', $params);
        }
        
        return $response;
    }

    /**
     * Actionn handler for viewing all InstitutionMedicalCenter of selected institution
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewMedicalCenterAction(Request $request)
    {
        if($request->get('center')) {
            $this->institution = $request->get('institution');
            $this->institutionMedicalCenter = $request->get('center');
            $this->request = $request;
        }
        
        $institutionMedicalCenterService = $this->get('services.institution_medical_center');
        $instSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $institutionSpecializations = $this->institutionMedicalCenter->getInstitutionSpecializations();
        $institutionSpecializationForm = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization(), array('em' => $this->getDoctrine()->getEntityManager()));
        $institutionMedicalSpecialistForm = $this->createForm(new \HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType());
        //globalAwards Form
        
        $institutionMedicalCenterService = $this->get('services.institution_medical_center');
        $form = $this->createForm(new InstitutionGlobalAwardsSelectorFormType());
        $currentGlobalAwards = $institutionMedicalCenterService->getGroupedMedicalCenterGlobalAwards($this->institutionMedicalCenter);
        $autocompleteSource = $this->get('services.global_award')->getAutocompleteSource();
        // get global ancillary services
        $ancillaryServicesData = array(
            'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
            'selectedAncillaryServices' => array()
        );
        
        foreach ($institutionMedicalCenterService->getMedicalCenterServices($this->institutionMedicalCenter) as $_selectedService) {
            $ancillaryServicesData['selectedAncillaryServices'][] = $_selectedService->getId();
        }
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
        
        $params = array(
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institutionSpecializations' => $institutionSpecializations,
            'institutionMedicalCenterForm' => $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => array('name','description','businessHours','city','country','zipCode','state','contactEmail','contactNumber','address','timeZone','websites')))->createView(),
            'institutionSpecializationFormName' => InstitutionSpecializationFormType::NAME,
            'institutionSpecializationForm' => $institutionSpecializationForm->createView(),
            'form' => $form->createView(),
            'institutionMedicalSpecialistForm' => $institutionMedicalSpecialistForm->createView(),
            'selectedSubMenu' => 'centers',
            'awardsSourceJSON' => \json_encode($autocompleteSource['award']),
            'certificatesSourceJSON' =>\json_encode($autocompleteSource['certificate']),
            'affiliationsSourceJSON' => \json_encode($autocompleteSource['affiliation']),
            'currentGlobalAwards' => $currentGlobalAwards,
            'editGlobalAwardForm' => $editGlobalAwardForm->createView(),
            'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView(),
            'accreditationsSourceJSON' => \json_encode($autocompleteSource['accreditation']),
            'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution),         
            'ancillaryServicesData' => $ancillaryServicesData,
            'sideBarUsed' => 'AdminBundle:InstitutionTreatments:sidebar.html.twig',
            'isOpen24hrs' => $this->get('services.institution_medical_center')->checkIfOpenTwentyFourHours(\json_decode($this->institutionMedicalCenter->getBusinessHours(),true)),
            //'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
            //'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions()
            //'routes' => DefaultController::getRoutes($this->request->getPathInfo())
//             'routes' => array(
//                             'gallery' => 'admin_institution_gallery',
//                             'media_edit_caption' => 'institution_media_edit_caption',
//                             'media_delete' => 'institution_media_delete'

            'routes' => MediaService::getRoutes($this->request->getPathInfo())
        );

        return $this->render('AdminBundle:InstitutionTreatments:viewMedicalCenter.html.twig', $params);
    }
    /*
     * ajax
    * This will add medicalSpecialist on InstitutionMedicalCenter
    */
    public function ajaxAddMedicalSpecialistAction(Request $request)
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
            
            $html = $this->renderView('AdminBundle:InstitutionTreatments:tableRow.specialist.html.twig', array('institution' => $this->institution,'institutionMedicalCenter' => $this->institutionMedicalCenter,'doctors' => array($specialist)));
            $response = new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
        }
        return $response;
        
    }
    
    public function addNewMedicalSpecialistAction(Request $request)
    {
        $this->institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
        $doctor = new Doctor();
        $specializations = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findByStatus(Specialization::STATUS_ACTIVE);
        $form = $this->createForm(new DoctorFormType(), $doctor);
        
        if ($this->getRequest()->isMethod('POST')) {
            $doctorData = $request->get('doctor');
            if($newMedia = $this->saveMedia($request->files->get('doctor'))) {
                $doctorData['media'] = $newMedia;
            } else {
                if($doctor->getId()) {
                    $doctorData['media'] = $media;
                }
            }
        
            $form->bind($doctorData);
        
            if($form->isValid()) {
                // Get contactNumbers and convert to json format
                $contactNumber = json_encode($request->get('contactNumber'));
        
                $doctor->setContactNumber($contactNumber);
                $doctor->setStatus(Doctor::STATUS_ACTIVE);
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($doctor);
                $em->flush();
        
                $this->institutionMedicalCenter->addDoctor($doctor);
                $center = $this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
                
                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_ajaxAddMedicalSpecialist',array("institutionId" => $this->institution->getId(), "imcId" => $request->get('imcId'))));
            }
        }
        return $this->render('AdminBundle:Doctor:common.form.html.twig', array(
                        'form' => $form->createView(),
                        'institution' => $this->institution,
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'specializations' => $specializations
        ));
    }
        
    private function saveMedia($fileBag)
    {
        if($fileBag['media']) {
            $media = $this->get('services.media')->uploadDoctorImage($fileBag['media']);
            return $media;
        }
    
        return null;
    }
    
    public function ajaxRemoveMedicalSpecialistAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId', 0));
        if (!$doctor) {
            throw $this->createNotFoundException('Invalid doctor.');
        }
    
        $form = $this->createForm(new \HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType(), $doctor);
        
        if ($request->isMethod('POST'))  {
            $form->bind($request);
            if ($form->isValid()) {
                $_id = $doctor->getId();
                //$this->institutionMedicalCenter->removeDoctor($doctor);
                //$this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
                $response = new Response(\json_encode(array('id' => $_id)), 200, array('content-type' => 'application/json'));
            }
            else {
                $response = new Response("Invalid form", 400);
            }
        }
        else {
    
            return $this->render('InstitutionBundle:Widgets:modal.deleteMedicalSpecialist.html.twig', array(
                            'institutionId' => $this->institution->getId(),
                            'institutionMedicalCenter' => $this->institutionMedicalCenter,
                            'doctor' => $doctor,
                            'form' => $form->createView()
            ));
    
            //$response = new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
        }
    
        return $response;
    
    }
    /**
     * Ajax handler for searching available doctors for an InstitutionMedicalCenter in HCA Data
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
            $doctorArr[] = array('value' => $each['first_name'] ." ". $each['last_name'], 'id' => $each['id'], 'path' => $this->generateUrl('admin_doctor_load_doctor_specializations', array('doctorId' =>  $each['id'])));
        }
        
        return new Response(\json_encode($doctorArr, JSON_HEX_APOS), 200, array('content-type' => 'application/json'));
    }
    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMedicalCenterAction()
    {
        $institutionService = $this->get('services.institution');
        $isSingleCenter = $institutionService->isSingleCenter($this->institution);
        
        // if this is a single center institution , we will not allow to add another medical center
        if($isSingleCenter && $institutionService->getFirstMedicalCenter($this->institution)) {
            
            return $this->redirect($this->generateUrl('admin_institution_manageCenters',array('institutionId' => $this->institution->getId())));
        }
        else {
            $service = $this->get('services.institution_medical_center');
            $request = $this->request;
            if (is_null($this->institutionMedicalCenter)) {
                $this->institutionMedicalCenter = new institutionMedicalCenter();
                $this->institutionMedicalCenter->setInstitution($this->institution);
            }
            else {
                // there is an imcId in the Request, check if this is a draft
                if ($this->institutionMedicalCenter && !$service->isDraft($this->institutionMedicalCenter)) {
    
                    $request->getSession()->setFlash('error', 'Invalid medical center draft.');
    
                    return $this->redirect($this->generateUrl('admin_institution_manageCenters', array('institutionId' => $this->institution->getId())));
                }
            }
    
            $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution),$this->institutionMedicalCenter, array('is_hidden' => false));
     
            if ($request->isMethod('POST')) {
                $form->bind($this->request);
                
                if ($form->isValid()) {
    
                    $form->getData()->setAddress('');
                    $this->institutionMedicalCenter = $service->saveAsDraft($form->getData());
    
                    
                    // dispatch event
                    $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER,
                        $this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER, $this->institutionMedicalCenter, array('institutionId' => $this->institution->getId())
                    ));
    
                    $this->request->getSession()->setFlash('success', '"' . $this->institutionMedicalCenter->getName() . '"' . " has been created. You can now add Specializations to this center.");
    
                    return $this->redirect($this->generateUrl('admin_institution_medicalCenter_addSpecialization',array(
                        'institutionId' => $this->institution->getId(),
                        'imcId' => $this->institutionMedicalCenter->getId()
                    )));
                }
            }
    
            $params = array(
                'form' => $form->createView(),
                'institution' => $this->institution,
                'institutionMedicalCenter' => $this->institutionMedicalCenter,
                'selectedSubMenu' => 'centers',
                'isNew' => true,
                'formAction' => $this->generateUrl('admin_institution_medicalCenter_add')
            );
    
            return $this->render('AdminBundle:InstitutionTreatments:form.medicalCenter.html.twig', $params);
        }
    }

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
            //$html = $this->renderView('InstitutionBundle:MedicalCenter/Widgets:businessHoursTable.html.twig', array('institutionMedicalCenter' => $this->institutionMedicalCenter));
            $html = $this->renderView('AdminBundle:Widgets:businessHours.html.twig', array('institutionMedicalCenter' => $this->institutionMedicalCenter));
            
            $response = new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
        }
        catch (\Exception $e) {
            $response = new Response($e->getMessage(), 500);
        }
        
        return $response;
    }
    
    public function editMedicalCenterAction(Request $request)
    {
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => array('city', 'country','zipCode','state','timeZone','status')));
        $template = 'AdminBundle:InstitutionTreatments:form.MedicalCenter.html.twig';
        if ($request->isMethod('POST')) {
            $form->bind($this->request);
        
            if ($form->isValid()) {
                $this->get('services.institution_medical_center')->save($form->getData());
                $request->getSession()->setFlash('success', '"'.$this->institutionMedicalCenter->getName().'" has been updated!');
            }
        }
        
        return $this->render($template, array(
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'institution' => $this->institution,
                        'form' => $form->createView(),
                        'formAction' => $this->generateUrl('admin_institution_medicalCenter_edit',array('imcId' => $this->institutionMedicalCenter->getId(), 
                                        'institutionId' => $this->institution->getId()))
        ));
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateMedicalCenterAction()
    {
        if($this->request->get('description')) {
            $description = $this->request->get('description');
            $this->institutionMedicalCenter->setDescription($description);
        }

        if($this->request->get('name')) {
            $name = $this->request->get('name');
            $this->institutionMedicalCenter->setName($name);
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institutionMedicalCenter);
        $result = $em->flush();

        // dispatch event
        //$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER,
            //$this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER, $this->institutionMedicalCenter, array('institutionId' => $this->institution->getId())
        //));

        $response = new Response (json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function editMedicalCenterStatusAction(Request $request)
    {
        
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => array('name','description','businessHours','city','country','zipCode','state','contactEmail','contactNumber','address','timeZone','websites')));
        //$form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => array('city', 'country','zipCode','state','timeZone')));
        $template = 'AdminBundle:InstitutionTreatments:edit.medicalCenter.html.twig';
        $output = array();
        if ($request->isMethod('POST')) {
            $form->bind($this->request);
            if ($form->isValid()) {
                $this->get('services.institution_medical_center')->save($form->getData());
                $request->getSession()->setFlash('success', '"'.$this->institutionMedicalCenter->getName().'" has been updated!');
            }
        }
        else {
            $output['html'] =  $this->renderView($template, array(
                            'institutionMedicalCenter' => $this->institutionMedicalCenter,
                            'institution' => $this->institution,
                            'institutionMedicalCenterForm' => $form->createView()
            ));
        }
                                // \json_encode($output),200, array('content-type' => 'application/json'));
        $response = new Response(\json_encode($output),200, array('content-type' => 'application/json'));
        return $response;
    }

    /**
     *
     * @param unknown_type $institutionId
     * @param unknown_type $imcId
     */
    public function centerSpecializationsAction()
    {
        $instSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $specializations = $instSpecializationRepo->getByInstitutionMedicalCenter($this->institutionMedicalCenter);

        $params = array('specializations' => $specializations);

        return $this->render('AdminBundle:InstitutionTreatments:centerSpecializations.html.twig', $params);
    }
    
    public function ajaxAddSpecializationTreatmentAction(Request $request)
    {
        $institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')
            ->find($request->get('isId', 0));
        $treatment = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')->find($request->get('tId', 0));
        
        if (!$institutionSpecialization) {
            throw $this->createNotFoundException("Invalid institution specialization {$institutionSpecialization->getId()}.");
        }
        if (!$treatment) {
            throw $this->createNotFoundException("Invalid treatment {$treatment->getId()}.");
        }
        
        $institutionSpecialization->addTreatment($treatment);
        
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionSpecialization);
            $em->flush();
            
            $output = array(
                'link' => array(
                    'href' => $this->generateUrl('admin_institution_medicalCenter_ajaxRemoveSpecializationTreatment', array(
                        'tId' => $treatment->getId(),
                        'institutionId' => $this->institution->getId(),
                        'isId' =>  $institutionSpecialization->getId())
                    ),
                    'html' => 'Delete'
                ),
                'icon' => 'icon-trash'
            );
            
            $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
        }
        catch (\Exception $e) {
            $response = new Response($e->getMessage(), 500);
        }
        
        return $response;
    }


    /**
     * Add a new specialization to medical center
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addSpecializationAction(Request $request)
    {
        $service = $this->get('services.institution_medical_center');

        if (!$this->institutionMedicalCenter) {
            throw $this->createNotFoundException('Invalid institutionMedicalCenter');
        }
        
        if ($this->request->isMethod('POST')) {
        
            $submittedSpecializations = $this->request->get(InstitutionSpecializationFormType::NAME);
            $em = $this->getDoctrine()->getEntityManager();
            $errors = array();
            if (\count($submittedSpecializations) > 0) {
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
            }
            else {
                $errors[] = 'Please provide at least one specialization.';
            }
            
            if (\count($errors) > 0) {
                $request->getSession()->setFlash('notice', '<ul><li>'.\implode('</li><li>', $errors).'</li></ul>'); 
            }
            
            $response = $this->redirect($this->generateUrl('admin_institution_medicalCenter_view', array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId())));
            
        }
        else {
            $form = $this->createForm(new InstitutionSpecializationSelectorFormType());
            $assignedSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->findByInstitutionMedicalCenter($this->institutionMedicalCenter);
            $specializations = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getAvailableSpecializations($assignedSpecialization);
            $specializationArr = array();
            foreach ($specializations as $e) {
                $specializationArr[] = array('value' => $e->getName(), 'id' => $e->getId());
            }
            
            $params = array(
                'form' => $form->createView(),
                'institution' => $this->institution,
                'institutionMedicalCenter' => $this->institutionMedicalCenter,
                'selectedSubMenu' => 'centers',
                'specializationsJSON' => \json_encode($specializationArr),
            );
            
            $response = $this->render('AdminBundle:InstitutionTreatments:addSpecializations.html.twig', $params);
        }
        
        return $response; 
    }
    /**
     * Add a new specialization to medical center
     *
     */
    public function ajaxAddSpecializationAction(Request $request)
    {
        $specializationId = $request->get('specializationId', 0);
        $criteria = array('status' => Specialization::STATUS_ACTIVE, 'id' => $specializationId);
    
        $params['specialization'] = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findOneBy($criteria);
    
        if(!$params['specialization']) {
            $result = array('error' => 'Invalid Specialization');
    
            return new Response('Invalid Specialization', 404);
        }
    
        $groupBySubSpecialization = true;
        $form = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization(), array('em' => $this->getDoctrine()->getEntityManager()));
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
     *
     * @param unknown_type $institutionId
     * @param unknown_type $imcId
     */
    public function addGlobalAwardsAction()
    {
        $form = $this->createForm(new InstitutionGlobalAwardFormType(),$this->institutionMedicalCenter);

         if ($this->request->isMethod('POST')) {
             $form->bind($this->request);

            if ($form->isValid()) {

                $this->institutionMedicalCenter = $this->get('services.institutionMedicalCenter')
                ->saveAsDraft($form->getData());

                $this->request->getSession()->setFlash('success', "GlobalAward has been saved!");

                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_view',
                                array('imcId' => $this->institutionMedicalCenter->getId(), 'institutionId' => $this->institution->getId())));
            }
        }
        return $this->render('AdminBundle:InstitutionTreatments:addGlobalAward.html.twig', array(
                        'form' => $form->createView(),
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'institution' => $this->institution
        ));
    }

    /**
     *
     * @param unknown_type $institutionId
     * @param unknown_type $imcId
     */
    public function updateGlobalAwardsAction()
    {
        $request = $this->getRequest();

        if (!$this->institutionMedicalCenter) {
            throw $this->createNotFoundException('Invalid institutionMedicalCenter');
        }

           $institutionGlobalAwardRepo = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward');
        $institutionGlobalAwardRepo->updateGlobalAwards($request->get('global_awardId'), $this->institutionMedicalCenter->getId());

        $this->request->getSession()->setFlash('success', "GlobalAward has been removed!");

        return $this->redirect($this->generateUrl('admin_institution_medicalCenter_view',
               array('imcId' => $this->institutionMedicalCenter->getId(), 'institutionId' => $this->institution->getId())));
    }

    /**
     * Add specialization and treatments to an institution medical center
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addInstitutionTreatmentsAction()
    {
        $service = $this->get('services.institution_medical_center');
        // this should only be accessed by draft
        if (!$service->isDraft($this->institutionMedicalCenter)) {
            $this->request->getSession()->setFlash('error', 'Invalid medical center draft.');

            // return $this->redirect($this->generateUrl('admin_institution_manageCenters', array('institutionId' => $this->institution->getId())));
        }

        $institutionSpecialization = new InstitutionSpecialization();

        $params = array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter
        );

        return $this->render('AdminBundle:InstitutionTreatments:addInstitutionTreatments', $params);
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
     * @author acgvelarde
     */
    public function ajaxRemoveAncillaryServiceAction(Request $request)
    {
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')
            ->find($request->get('asId', 0));
        
        if (!$ancillaryService) {
            throw $this->createNotFoundException('Invalid ancillary service id');
        }
        
        $propertyService = $this->get('services.institution_medical_center_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);

        // get property value for this ancillary service
        $property = $this->get('services.institution_medical_center')->getPropertyValue($this->institutionMedicalCenter, $propertyType, $ancillaryService->getId());
        
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($property);
            $em->flush();
        
            $output = array(
                'html' => $this->renderView('AdminBundle:InstitutionTreatments:row.ancillaryService.html.twig', array(
                    'institution' => $this->institution,
                    'institutionMedicalCenter' => $this->institutionMedicalCenter,
                    'ancillaryService' => $ancillaryService,
                    '_isSelected' => false
                )),
                'error' => 0
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
     * @author acgvelarde
     */
    public function ajaxAddAncillaryServiceAction(Request $request)
    {
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')
            ->find($request->get('asId', 0));
    
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
                    'html' => $this->renderView('AdminBundle:InstitutionTreatments:row.ancillaryService.html.twig', array(
                            'institution' => $this->institution,
                            'institutionMedicalCenter' => $this->institutionMedicalCenter,
                            'ancillaryService' => $ancillaryService,
                            '_isSelected' => true
                        )),
                    'error' => 0
                );
                $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
            }
            catch (\Exception $e){
                $response = new Response($e->getMessage(), 500);
            }
            
        }
    
        return $response;
    }
    public function ajaxAddGlobalAwardAction(Request $request)
    {
        $award = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($request->get('id'));
    
        if (!$award) {
            throw $this->createNotFoundException();
        }
    
        $propertyService = $this->get('services.institution_medical_center_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
    
        // check if this medical center already have this property
        if ($this->get('services.institution_medical_center')->hasPropertyValue($this->institutionMedicalCenter, $propertyType, $award->getId())) {
            $response = new Response("Award {$award->getId()} already exists.", 500);
        }
        else {
            $property = $propertyService->createInstitutionMedicalCenterPropertyByName($propertyType->getName(), $this->institution, $this->institutionMedicalCenter);
            $property->setValue($award->getId());
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($property);
                $em->flush();
    
                $html = $this->renderView('AdminBundle:InstitutionTreatments/Partials:row.globalAward.html.twig', array(
                    'award' => $award,
                    'property' => $property,
                    'institution' => $this->institution,
                    'institutionMedicalCenter' => $this->institutionMedicalCenter,
                    'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView(),
                ));
    
                $response = new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
            }
            catch (\Exception $e){
                $response = new Response($e->getMessage(), 500);
            }
        }
    
        return $response;
    }
    
    public function ajaxRemoveGlobalAwardAction(Request $request)
    {
        $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->find($request->get('id'));
        
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }

        $form = $this->createForm(new CommonDeleteFormType(), $property);
    
        if ($request->isMethod('POST'))  {
            $form->bind($request);
            if ($form->isValid()) {
    
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($property);
                $em->flush();
                
                $response = new Response(\json_encode(array('id' => $request->get('id'))), 200, array('content-type' => 'application/json'));
            }
            else{
                $response = new Response("Invalid form", 400);
            }
        }
    
        return $response;
    }
    
    public function ajaxEditGlobalAwardAction()
    {
        $globalAward = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->find($this->request->get('globalAwardId', 0));
        if (!$globalAward) {
            throw $this->createNotFoundException('Invalid global award');
        }
        $propertyType = $this->get('services.institution_property')->getAvailablePropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD);
        $imcProperty = $this->get('services.institution_medical_center')->getPropertyValue($this->institutionMedicalCenter, $propertyType, $globalAward->getId());
        $imcProperty->setValueObject($globalAward);
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType(), $imcProperty);
        if ($this->request->isMethod('POST')) {
            $editGlobalAwardForm->bind($this->request);
            if ($editGlobalAwardForm->isValid()) {
                try {
                    $imcProperty = $editGlobalAwardForm->getData();
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->persist($imcProperty);
                    $em->flush();
                    $extraValue = \json_decode($imcProperty->getExtraValue(), true);
                    $yearAcquired = \implode(', ',$extraValue[InstitutionGlobalAwardExtraValueDataTransformer::YEAR_ACQUIRED_JSON_KEY]);
                    $output = array(
                                    'targetRow' => '#globalAwardRow_'.$imcProperty->getId(),
                                    'html' => $yearAcquired
                    );
                    $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
                }
                catch(\Exception $e) {
                    $response = new Response('Error: '.$e->getMessage(), 500);
                }
            }
            else {
                $response = new Response('Form error'.$e->getMessage(), 400);
            }
        }
    
        return $response;
    }
}