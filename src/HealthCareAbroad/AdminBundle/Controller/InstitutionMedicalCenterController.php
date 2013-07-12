<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use Symfony\Component\HttpFoundation\Response;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\PagerBundle\Pager;

use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\HelperBundle\Form\FieldType\FancyBusinessHourType;

use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;

use HealthCareAbroad\AdminBundle\Form\InstitutionMedicalCenterFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardsSelectorFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\AdminBundle\Form\InstitutionFormType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
class InstitutionMedicalCenterController extends Controller
{
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
    
    public function indexAction()
    {
        $institutionService = $this->get('services.institution');
        $institutionStatusForm = $this->createForm(new InstitutionFormType(), $this->institution, array(InstitutionFormType::OPTION_REMOVED_FIELDS => array('name','description','contactEmail','contactNumber','websites')));
        if($institutionService->isSingleCenter($this->institution)) {
            $firstMedicalCenter = $institutionService->getFirstMedicalCenter($this->institution);
            if ($firstMedicalCenter) {
                $params = array('institutionId' => $this->institution->getId(), 'imcId' => $firstMedicalCenter->getId());
                
                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_view', $params));
            }
            else {
                $this->request->getSession()->setFlash('notice', 'No medical centers yet, add a clinic now.');
                $response = $this->redirect($this->generateUrl('admin_institution_medicalCenter_add', array('institutionId' => $this->institution->getId())));
            }
    
        }
        else {
            $institutionMedicalCenters = $this->filteredResult;//$institutionService->getAllMedicalCenters($this->institution);
    
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
                            'isSingleCenter' => false,
                            'institutionStatusForm' =>$institutionStatusForm->createView()
            );
    
            $response = $this->render('AdminBundle:InstitutionMedicalCenter:index.html.twig', $params);
        }
    
        return $response;
    }
    
    /**
     * Action handler for viewing selected center
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request)
    {
        if($request->get('center')) {
            $this->institution = $request->get('institution');
            $this->institutionMedicalCenter = $request->get('center');
            $this->request = $request;
        }
    
        $institutionMedicalCenterService = $this->get('services.institution_medical_center');
        $instSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $institutionSpecializations = $this->institutionMedicalCenter->getInstitutionSpecializations();
        $institutionSpecializationForm = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization());
        $institutionMedicalSpecialistForm = $this->createForm(new InstitutionDoctorSearchFormType());
        //globalAwards Form
    
        //services
        $assignedServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllServicesByInstitutionMedicalCenter($this->institutionMedicalCenter);
        $institutionAncillaryServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->getAvailableInstitutionServicesByInstitutionMedicalCenter($this->institution, $this->institutionMedicalCenter, $assignedServices);
    
        $businessFancyForm = $this->createForm(new FancyBusinessHourType());
    
        $institutionMedicalCenterService = $this->get('services.institution_medical_center');
        $form = $this->createForm(new InstitutionGlobalAwardsSelectorFormType());
        $currentGlobalAwards = $institutionMedicalCenterService->getGroupedMedicalCenterGlobalAwards($this->institutionMedicalCenter);
        $autocompleteSource = $this->get('services.global_award')->getAutocompleteSource();
    
        // get global,current and selcted ancillary services
        $ancillaryServicesData = array(
                        'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                        'selected' => array(),
                        'currentAncillaryData' => array()
        );
        $ancillaryServicesData = $this->get('services.institution_medical_center_property')->getCurrentAndSelectedAncillaryServicesByPropertyType($this->institutionMedicalCenter, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE, $ancillaryServicesData);
    
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
    
        $adapter = new ArrayAdapter($this->institutionMedicalCenter->getMedia()->toArray());
        $institutionMedicalCenterMedia = new Pager($adapter, array('page' => $request->get('page'), 'limit' => 5));
        $this->institutionMedicalCenter->addContactDetail(new ContactDetail());
        $params = array(
                        'institution' => $this->institution,
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'institutionSpecializations' => $institutionSpecializations,
                        'institutionMedicalCenterForm' => $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => array('name','description','city','country','zipCode','state','contactEmail','contactNumber','address','timeZone','websites')))->createView(),
                        'institutionSpecializationFormName' => InstitutionSpecializationFormType::NAME,
                        'institutionSpecializationForm' => $institutionSpecializationForm->createView(),
                        'form' => $form->createView(),
                        'fancyBusinessForm' => $businessFancyForm->createView(),
                        'institutionServices' => $institutionAncillaryServices,
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
                        'sideBarUsed' => 'AdminBundle:InstitutionMedicalCenter/Widgets:sidebar.html.twig',
                        //'isOpen24hrs' => $this->get('services.institution_medical_center')->checkIfOpenTwentyFourHours(\json_decode($this->institutionMedicalCenter->getBusinessHours(),true)),
                        'institutionMedicalCenterMedia' => $institutionMedicalCenterMedia
        );
    
        return $this->render('AdminBundle:InstitutionMedicalCenter:view.html.twig', $params);
    }
    
    /**
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function editStatusAction(Request $request)
    {
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => array('name','description','businessHours','city','country','zipCode','state','contactEmail','contactDetails','address','timeZone','websites','socialMediaSites','addressHint')));
        $template = 'AdminBundle:InstitutionMedicalCenter:edit.medicalCenter.html.twig';
        $output = array();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $this->get('services.institution_medical_center')->save($form->getData());
                $output['html'] = array('success' => $this->institutionMedicalCenter->getName().'status has been updated!');
    
                if (InstitutionMedicalCenterStatus::APPROVED == $form->getData()->getStatus()) {
                    // TODO: Update this when we have formulated a strategy for our events system
                    // We can't use InstitutionBundleEvents; we don't know the consequences of the event firing up other listeners.
                    $this->get('event_dispatcher')->dispatch(
                                    MailerBundleEvents::NOTIFICATIONS_NEW_LISTINGS_APPROVED, new GenericEvent($this->institutionMedicalCenter)
                    );
                }
            }
            else {
                $errors = array();
                foreach ($form->getErrors() as $_err) {
                    $errors[] = $_err->getMessage();
                }
                $response = new Response(\json_encode(array('html' => \implode('<br />', $errors))), 400, array('content-type' => 'application/json'));
            }
        }
        else {
            $output['html'] =  $this->renderView($template, array(
                            'institutionMedicalCenter' => $this->institutionMedicalCenter,
                            'institution' => $this->institution,
                            'institutionMedicalCenterForm' => $form->createView()
            ));
        }
        $response = new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    
        return $response;
    }
    
    public function editAction(Request $request)
    {
        $institutionMedicalCenterService = $this->get('services.institution_medical_center');
        $template = 'AdminBundle:InstitutionMedicalCenter:form.html.twig';
        $this->get('services.contact_detail')->initializeContactDetails($this->institutionMedicalCenter, array(ContactDetailTypes::PHONE));
    
        if ($request->isMethod('POST')) {
    
            $formVariables = $this->request->get(InstitutionMedicalCenterFormType::NAME);
            unset($formVariables['_token']);
            $removedFields = \array_diff(InstitutionMedicalCenterFormType::getFieldNames(), array_keys($formVariables));
    
            $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution),$this->institutionMedicalCenter, array(
                            InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false,
                            InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => $removedFields
            ));
    
            $form->bind($this->request);
    
            if ($form->isValid()) {
                $this->institutionMedicalCenter = $form->getData();
                $this->get('services.contact_detail')->removeInvalidContactDetails($this->institutionMedicalCenter);
                $this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
                $request->getSession()->setFlash('success', '"'.$this->institutionMedicalCenter->getName().'" has been updated!');
    
                if(!$this->institutionMedicalCenter->getContactDetails()->count()) {
                    $phoneNumber = new ContactDetail();
                    $phoneNumber->setType(ContactDetailTypes::PHONE);
                    $this->institutionMedicalCenter->addContactDetail($phoneNumber);
                }
            }
        }
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter);
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
    public function addAction()
    {
    
        $institutionService = $this->get('services.institution');
        $isSingleCenter = $institutionService->isSingleCenter($this->institution);
    
        // if this is a single center institution , we will not allow to add another medical center
        if($isSingleCenter && $institutionService->getFirstMedicalCenter($this->institution)) {
            return $this->redirect($this->generateUrl('admin_institution_medicalCenter_index',array('institutionId' => $this->institution->getId())));
        }
        else {
    
            $service = $this->get('services.institution_medical_center');
            $request = $this->request;
            if (is_null($this->institutionMedicalCenter)) {
    
                $this->institutionMedicalCenter = new InstitutionMedicalCenter();
                $this->institutionMedicalCenter->setInstitution($this->institution);
            }
            else {
                // there is an imcId in the Request, check if this is a draft
                if ($this->institutionMedicalCenter && !$service->isDraft($this->institutionMedicalCenter)) {
                    $request->getSession()->setFlash('error', 'Invalid medical center draft.');
    
                    return $this->redirect($this->generateUrl('admin_institution_medicalCenter_index', array('institutionId' => $this->institution->getId())));
                }
            }
            $this->get('services.contact_detail')->initializeContactDetails($this->institutionMedicalCenter, array(ContactDetailTypes::PHONE));
    
            $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution),$this->institutionMedicalCenter, array('is_hidden' => false,InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false));
    
            if ($request->isMethod('POST')) {
                $form->bind($this->request);
    
                if ($form->isValid()) {
                    $this->get('services.contact_detail')->removeInvalidContactDetails($this->institutionMedicalCenter);
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
                            'formAction' => $this->generateUrl('admin_institution_medicalCenter_add', array('institutionId' => $this->institution->getId()))
            );
    
            return $this->render('AdminBundle:InstitutionMedicalCenter:form.html.twig', $params);
        }
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
            $doctorArr[] = array('value' => $each['first_name'] ." ". $each['last_name'] . " - " . $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->getSpecializationListByMedicalSpecialist($each['id']) , 'id' => $each['id'], 'specializations' => $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->getSpecializationListByMedicalSpecialist($each['id']), 'path' => $this->generateUrl('admin_doctor_specializations', array('doctorId' =>  $each['id'])));
    
        }
    
        return new Response(\json_encode($doctorArr, JSON_HEX_APOS), 200, array('content-type' => 'application/json'));
    }
    
    /*
     * Ajax Handler that adds medicalSpecialist to InstitutionMedicalCenter
    */
    public function ajaxAddMedicalSpecialistAction(Request $request)
    {
        $specialist = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('id'));
    
        if (!$specialist) {
            throw $this->createNotFoundException();
        }
        
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
    /*
     * Ajax Handler that removes MedicalSpecialist to InstitutionMedicalCenter
     */
    public function ajaxRemoveMedicalSpecialistAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId', 0));
        if (!$doctor) {
            throw $this->createNotFoundException('Invalid doctor.');
        }
    
        $form = $this->createForm(new CommonDeleteFormType(), $doctor);
    
        if ($request->isMethod('POST'))  {
            $form->bind($request);
            if ($form->isValid()) {
                $_id = $doctor->getId();
                $this->institutionMedicalCenter->removeDoctor($doctor);
                $this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
                $response = new Response(\json_encode(array('id' => $_id)), 200, array('content-type' => 'application/json'));
            }
            else {
                $response = new Response("Invalid form", 400);
            }
        }
        else {
    
            return $this->render('InstitutionBundle:Widgets/Profile:doctor.confirmDelete.html.twig', array(
                            'institutionMedicalCenter' => $this->institutionMedicalCenter,
                            'doctor' => $doctor,
                            'form' => $form->createView()
            ));
        }
    
        return $response;
    }
    
    /**
     * Upload InstitutionMedicalCenter Logo
     * @param Request $request
     */
    public function uploadLogoAction(Request $request)
    {
        if (($fileBag = $request->files) && $fileBag->has('file')) {
    
            $media = $this->get('services.institution.media')->medicalCenterUploadLogo($fileBag->get('file'), $this->institutionMedicalCenter);
            if(!$media) {
                $this->get('session')->setFlash('error', 'Unable to Upload Logo');
            }
        }
    
        return $this->redirect($request->headers->get('referer'));
    }
    
    /**
     * Upload InstitutionMedicalCenter Media for Gallery
     * @param Request $request
     */
    public function uploadMediaAction(Request $request)
    {
        $response = new Response(json_encode(true));
        $response->headers->set('Content-Type', 'application/json');
    
        if (($fileBag = $request->files) && $fileBag->has('file')) {
            $media = $this->get('services.institution.media')->medicalCenterUploadToGallery($fileBag->get('file'), $this->institutionMedicalCenter);
            if(!$media) {
                $response = new Response('Error', 500);
            }
        }
    
        return $response;
    }
    
    public function addMediaAction(Request $request)
    {
        $formParams = array('institutionId' => $this->institution->getId());
    
        $formParams['imcId'] = $request->get('imcId');
        $uploadFormAction = $this->generateUrl('admin_institution_medicalCenter_media_upload', $formParams);
    
        return $this->render('AdminBundle:Institution:addMedia.html.twig', array(
                        'institution' => $this->institution,
                        'uploadFormAction' => $uploadFormAction,
                        'multiUpload' => $request->get('multiUpload')
        ));
    }
    
    public function ajaxAddBusinessHoursAction(Request $request)
    {
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => array('name','status','description','city','country','zipCode','state','addressHint','contactEmail','contactDetails','address','timeZone','websites','socialMediaSites')));
        $result = '';
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $institutionMedicalCenterService = $this->get('services.institution_medical_center');
                $institutionMedicalCenterService->clearBusinessHours($this->institutionMedicalCenter);
    
                $this->institutionMedicalCenter = $form->getData();
                foreach ($this->institutionMedicalCenter->getBusinessHours() as $_hour ) {
                    $_hour->setInstitutionMedicalCenter($this->institutionMedicalCenter );
                }
    
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($this->institutionMedicalCenter);
                $result = $em->flush();
            }
        }
    
        $response = new Response(\json_encode($result),200, array('content-type' => 'application/json'));
    
        return $response;
    
    }
    
    /*
     * Ajax Handler that update status for PayingClient on InstitutionMedicalCenter
     */
    public function ajaxUpdatePayingClientAction(Request $request)
    {
        $request = $this->getRequest();
        $this->institutionMedicalCenter->setPayingClient((int)$request->get('payingClient'));
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institutionMedicalCenter);
        $em->flush($this->institutionMedicalCenter);
        
        return new Response(\json_encode(true),200, array('content-type' => 'application/json'));
    }
    
}
