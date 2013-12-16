<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Entity\PayingStatus;

use HealthCareAbroad\AdminBundle\Form\InstitutionProfileFormType;

use Symfony\Component\EventDispatcher\GenericEvent;

use HealthCareAbroad\MailerBundle\Event\MailerBundleEvents;

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
        if($this->request->get('institutionId')){
            $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId', 0));
            // Check Institution
            if(!$this->institution) {
                throw $this->createNotFoundException('Invalid Institution');
            }
        }
        // check InstitutionMedicalCenter
        if ($this->request->get('imcId')) {
            $institutionMedicalCenterId = $this->request->get('imcId');

            $eagerLoad = false;
            if($this->request->attributes->get('_route') == 'admin_institution_medicalCenter_view') {
                $eagerLoad = array(
                    'media' => 'a.media',
                    'doctors' => 'a.doctors',
                    'businessHours' => 'a.businessHours',
                    'contactDetails' => 'a.contactDetails',
                    'docSpecializations' => 'doctors.specializations',
                );
            } else if ($this->request->attributes->get('_route') == 'admin_institution_medicalCenter_edit') {
                $eagerLoad = array('businessHours' => 'a.businessHours', 'contactDetails' => 'a.contactDetails');

            }

            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($institutionMedicalCenterId, $eagerLoad);            
            
            if (!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid institution medical center');
            }
        }
    }

    public function indexAction()
    {
        $institutionService = $this->get('services.institution');
        $institutionStatusForm = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionFormType::OPTION_REMOVED_FIELDS => array('name','description','contactEmail','contactNumber','websites')));
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
            $institutionMedicalCenters = $this->filteredResult;

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
                'institutionStatusForm' => $institutionStatusForm->createView(),
                'payingClientStatusChoices' => PayingStatus::all(),
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
        $institutionSpecializations = $institutionMedicalCenterService->getActiveSpecializations($this->institutionMedicalCenter);
        $institutionSpecializationForm = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization());
        $institutionMedicalSpecialistForm = $this->createForm(new InstitutionDoctorSearchFormType());
        //globalAwards Form

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
                        'institutionMedicalCenterMedia' => $institutionMedicalCenterMedia,
                        'payingClientStatusChoices' => PayingStatus::all(),
        );

        return $this->render('AdminBundle:InstitutionMedicalCenter:view.html.twig', $params);
    }

    /**
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function editStatusAction(Request $request)
    {
        if ($request->isMethod('POST')) {

            $formData = $this->getRequest()->get('institutionMedicalCenter');

            if (InstitutionMedicalCenterStatus::isValid($formData['status'])) {

                if(InstitutionMedicalCenterStatus::APPROVED == $formData['status'] && $this->institution->getStatus() != InstitutionStatus::getBitValueForActiveAndApprovedStatus()) {
                    return new Response(\json_encode(array('error' => 'Unable to approve this Clinic! You must approve the Institution first.')), 400, array('content-type' => 'application/json'));
                }

                $this->get('services.institution_medical_center')->updateStatus($this->institutionMedicalCenter, $formData['status']);

                $output = array('message' => $this->institutionMedicalCenter->getName().'status has been updated!');

                // dispatch institutionMedicalCenter UPDATE_STATUS event
                $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_UPDATE_STATUS_INSTITUTION_MEDICAL_CENTER, new GenericEvent($this->institutionMedicalCenter));

                if (InstitutionMedicalCenterStatus::APPROVED == $formData['status']) {
                    $this->get('event_dispatcher')->dispatch(
                        MailerBundleEvents::NOTIFICATIONS_NEW_LISTINGS_APPROVED, new GenericEvent($this->institutionMedicalCenter)
                    );
                }

                // Invalidate InstitutionMedicalCenter Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));
                
                $response = new Response(\json_encode(array('success' => 'Status has been updated!')), 200, array('content-type' => 'application/json'));

            } else {
                $response = new Response(\json_encode(array('error' => 'Invalid status!')), 400, array('content-type' => 'application/json'));
            }
        } else {
            throw new NotFoundHttpException();
        }

        return $response;
    }

    public function editAction(Request $request)
    {
        $institutionMedicalCenterService = $this->get('services.institution_medical_center');
        $template = 'AdminBundle:InstitutionMedicalCenter:form.html.twig';
        $this->get('services.contact_detail')->initializeContactDetails($this->institutionMedicalCenter, array(ContactDetailTypes::PHONE),$this->institution->getCountry());

        if ($request->isMethod('GET')) {
            $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter);
        }
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

                // Invalidate InstitutionMedicalCenter Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

                // Invalidate Institution Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institution->getId()));

                $request->getSession()->setFlash('success', '"'.$this->institutionMedicalCenter->getName().'" has been updated!');
            }
        }
        
        return $this->render($template, array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institution' => $this->institution,
            'form' => $form->createView(),
            'sideBarUsed' => '',
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
            $this->get('services.contact_detail')->initializeContactDetails($this->institutionMedicalCenter, array(ContactDetailTypes::PHONE), $this->institution->getCountry());

            $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution),$this->institutionMedicalCenter, array('is_hidden' => false,InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false));

            if ($request->isMethod('POST')) {
                $form->bind($this->request);

                if ($form->isValid()) {
                    $this->get('services.contact_detail')->removeInvalidContactDetails($this->institutionMedicalCenter);
                    $form->getData()->setAddress('');
                    
                    // Temporary Code to mark a newly added clinic as added internally.
                    // Added By: Adelbert Silla
                    $this->institutionMedicalCenter->setIsFromInternalAdmin(1);

                    $this->institutionMedicalCenter = $service->saveAsDraft($form->getData());


                    // dispatch event
                    $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER,
                                    $this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER, $this->institutionMedicalCenter, array('institutionId' => $this->institution->getId())
                                    ));

                    // Invalidate InstitutionMedicalCenter Profile cache
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

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
        $doctors = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getAvailableDoctors($this->institutionMedicalCenter, \trim($request->get('term','')));
        $doctorArr = array();
        foreach ($doctors as $each) {
            $doctorArr[] = array(
                'value' => ucwords($each['last_name'] .", ". $each['first_name'] . " " . $each['middle_name'] . " " . $each['suffix']) . ' - '.$each['id'], 
                'id' => $each['id'], 
                'path' => $this->generateUrl('admin_doctor_specializations', array('doctorId' =>  $each['id'])));
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

            // Invalidate InstitutionMedicalCenter Profile cache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

            // Invalidate Institution Profile cache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));

            $html = $this->renderView('AdminBundle:InstitutionMedicalCenter/Partials:row.medicalSpecialist.html.twig', array('institution' => $this->institution,'institutionMedicalCenter' => $this->institutionMedicalCenter,'doctors' => array($specialist)));
            $response = new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));
        }
        return $response;

    }
    /*
     * Ajax Handler that removes MedicalSpecialist to InstitutionMedicalCenter
     */
    public function ajaxRemoveMedicalSpecialistAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('_doctorId', 0));
        if (!$doctor) {
            throw $this->createNotFoundException('Invalid doctor.');
        }

        if ($request->isMethod('POST'))  {
                $doctorId = $doctor->getId();
                $this->institutionMedicalCenter->removeDoctor($doctor);
                $this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
                
                // Invalidate InstitutionMedicalCenter Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

                // Invalidate Institution Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));

                $response = new Response(\json_encode(array('id' => $doctorId)), 200, array('content-type' => 'application/json'));
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
            
            // Invalidate InstitutionMedicalCenter Profile cache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));
            
            // Invalidate Institution Profile cache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));
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

            // Invalidate InstitutionMedicalCenter Profile cache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));
            
            // Invalidate Institution Profile cache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));
        }

        return $response;
    }

    public function addMediaAction(Request $request)
    {
        $formParams = array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId());

        $formParams['imcId'] = $request->get('imcId');
        $uploadFormAction = $this->generateUrl('admin_institution_medicalCenter_media_upload', $formParams);

        return $this->render('AdminBundle:Institution:addMedia.html.twig', array(
                        'institution' => $this->institution,
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
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
                if($request->get('isAlwaysOpen')){
                    $this->institutionMedicalCenter->setIsAlwaysOpen(1);
                    foreach ($this->institutionMedicalCenter->getBusinessHours() as $businessHours ) {
                        $this->institutionMedicalCenter->removeBusinessHour($businessHours);
                    }
                }
                else{
                    $this->institutionMedicalCenter->setIsAlwaysOpen(null);
                    $institutionMedicalCenterService = $this->get('services.institution_medical_center');
                    $institutionMedicalCenterService->clearBusinessHours($this->institutionMedicalCenter);

                    $this->institutionMedicalCenter = $form->getData();
                    foreach ($this->institutionMedicalCenter->getBusinessHours() as $_hour ) {
                        $_hour->setInstitutionMedicalCenter($this->institutionMedicalCenter );
                    }
                }

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($this->institutionMedicalCenter);
                $em->flush();
                
                // Invalidate InstitutionMedicalCenter Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

                // Invalidate Institution Profile cache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));
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

        try {
            $em = $this->getDoctrine()->getEntityManagerForClass('InstitutionBundle:InstitutionMedicalCenter');
            $em->persist($this->institutionMedicalCenter);
            $em->flush($this->institutionMedicalCenter);
            
            $this->get('services.institution')
                ->updatePayingClientStatus($this->institutionMedicalCenter->getInstitution(), $this->institutionMedicalCenter->getPayingClient());

            // Invalidate InstitutionMedicalCenter Profile cache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));
            
            // Invalidate Institution Profile cache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));

            $response = new Response(\json_encode(array('message' => 'ok')),200, array('content-type' => 'application/json'));
            
        }
        catch (\Exception $e) {
            $response = new Response(\json_encode(array('message' => $e->getMessage())),500, array('content-type' => 'application/json'));
        }

        return $response;
    }
    
    public function viewAllMedicalCentersAction(){
    
        $params = array(
            'pager' => $this->pager,
            'institutionMedicalCenters' => $this->filteredResult,
            'statusList' => InstitutionMedicalCenterStatus::getStatusList(),
        );
    
        return $this->render('AdminBundle:InstitutionMedicalCenter:viewAll.html.twig', $params);
    }
}
