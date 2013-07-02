<?php
/**
 *
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Form\FieldType\FancyBusinessHourType;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMediaService;

use HealthCareAbroad\PagerBundle\Pager;

use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use HealthCareAbroad\AdminBundle\Form\InstitutionFormType;

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

use HealthCareAbroad\AdminBundle\Form\InstitutionMedicalCenterFormType;

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
        $institutionStatusForm = $this->createForm(new InstitutionFormType(), $this->institution, array(InstitutionFormType::OPTION_REMOVED_FIELDS => array('name','description','contactEmail','contactNumber','websites')));
        if($institutionService->isSingleCenter($this->institution)) {
            $firstMedicalCenter = $institutionService->getFirstMedicalCenter($this->institution);
            if ($firstMedicalCenter) {
                // forward action to viewing a medical center
                //$response = $this->forward('AdminBundle:InstitutionTreatments:viewMedicalCenter',array('institution' => $this->institution, 'center' => $firstMedicalCenter, 'isOpen24hrs' => $this->get('services.institution_medical_center')->checkIfOpenTwentyFourHours(\json_decode($firstMedicalCenter->getBusinessHours(),true))));
                $params = array('institutionId' => $this->institution->getId(), 'imcId' => $firstMedicalCenter->getId());
                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_view', $params));
            }
            else {
                // no medical center yet, redirect to add medical center with 
                $this->request->getSession()->setFlash('notice', 'No medical centers yet, add a clinic now.');
                $response = $this->redirect($this->generateUrl('admin_institution_medicalCenter_add', array('institutionId' => $this->institution->getId())));
            }
                
        }
        else {
            $institutionMedicalCenters = $this->filteredResult;//$institutionService->getAllMedicalCenters($this->institution);
            
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
                'isSingleCenter' => false,
                'institutionStatusForm' =>$institutionStatusForm->createView()
            );
            
            $response = $this->render('AdminBundle:InstitutionTreatments:tabular.medicalCenters.html.twig', $params);
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
        $institutionSpecializationForm = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization());
        $institutionMedicalSpecialistForm = $this->createForm(new \HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType());
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
            'sideBarUsed' => 'AdminBundle:InstitutionTreatments:sidebar.html.twig',
            //'isOpen24hrs' => $this->get('services.institution_medical_center')->checkIfOpenTwentyFourHours(\json_decode($this->institutionMedicalCenter->getBusinessHours(),true)),
            'institutionMedicalCenterMedia' => $institutionMedicalCenterMedia
        );

        return $this->render('AdminBundle:InstitutionTreatments:viewMedicalCenter.html.twig', $params);
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
            $this->institutionMedicalCenter->addContactDetail(new ContactDetail());
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
                'formAction' => $this->generateUrl('admin_institution_medicalCenter_add', array('institutionId' => $this->institution->getId()))
            );

            return $this->render('AdminBundle:InstitutionTreatments:form.medicalCenter.html.twig', $params);
        }
    }

    public function editMedicalCenterAction(Request $request)
    {
        $institutionMedicalCenterService = $this->get('services.institution_medical_center');
        if(!$this->institutionMedicalCenter->getContactDetails()->count()) {
            $phoneNumber = new ContactDetail();
            $phoneNumber->setType(ContactDetailTypes::PHONE);
            $this->institutionMedicalCenter->addContactDetail($phoneNumber);
        }
       
        $template = 'AdminBundle:InstitutionTreatments:form.medicalCenter.html.twig';
        if ($request->isMethod('POST')) {
            
            $formVariables = $this->request->get(InstitutionMedicalCenterFormType::NAME);
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
            
            $form->bind($this->request);
        
            if ($form->isValid()) {
                $this->get('services.institution_medical_center')->save($form->getData());
                $request->getSession()->setFlash('success', '"'.$this->institutionMedicalCenter->getName().'" has been updated!');
            }
        }
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => array('city', 'country','zipCode','state','timeZone','status')));
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
        $template = 'AdminBundle:InstitutionTreatments:edit.medicalCenter.html.twig';
        $output = array();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $this->get('services.institution_medical_center')->save($form->getData());
                $output['html'] = array('success' => $this->institutionMedicalCenter->getName().'status has been updated!');
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

    /**
     *
     * @param unknown_type $institutionId
     * @param unknown_type $imcId
     */
    public function centerSpecializationsAction()
    {
        $instSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $specializations = $instSpecializationRepo->getByInstitutionMedicalCenter($this->institutionMedicalCenter);

        $params = array('specializations    ' => $specializations);
        return $this->render('AdminBundle:InstitutionTreatments:centerSpecializations.html.twig', $params);
    }
    
    public function ajaxAddBusinessHoursAction(Request $request)
    {
        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => array('name','status','description','city','country','zipCode','state','contactEmail','contactNumber','address','timeZone','websites')));
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
    /*
     * ajax
    * This will remove medicalSpecialist on InstitutionMedicalCenter
    */
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
                $this->institutionMedicalCenter->removeDoctor($doctor);
                $this->get('services.institution_medical_center')->save($this->institutionMedicalCenter);
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
        $property = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->find($request->get('id', 0));
        
        if (!$property) {
            throw $this->createNotFoundException('Invalid property.');
        }
        // get global,current and selcted ancillary services
        $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')->find($property->getValue());
        $ancillaryServicesData = array(
                        'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                        'selected' => array(),
                        'currentAncillaryData' => array()
        );
        $ancillaryServicesData = $this->get('services.institution_medical_center_property')->getCurrentAndSelectedAncillaryServicesByPropertyType($this->institutionMedicalCenter, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE, $ancillaryServicesData);
        
        try {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($property);
            $em->flush();
            
            $output = array(
                    'label' => 'Add Service',
                    'href' => $this->generateUrl('admin_institution_medicalCenter_ajaxAddAncillaryService', array('institutionId' => $this->institution->getId(),'imcId' => $this->institutionMedicalCenter->getId() ,'id' => $ancillaryService->getId() )),
                    'ancillaryServicesData' => $ancillaryServicesData,
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
     * @author acgvelarde
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
            // get global,current and selcted ancillary services
            $ancillaryServicesData = array(
                            'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                            'selected' => array(),
                            'currentAncillaryData' => array()
            );
            $ancillaryServicesData = $this->get('services.institution_medical_center_property')->getCurrentAndSelectedAncillaryServicesByPropertyType($this->institutionMedicalCenter, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE, $ancillaryServicesData);
            
            try {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($property);
                $em->flush();
                
                $output = array(
                    'label' => 'Delete Service',
                    'href' => $this->generateUrl('admin_institution_medicalCenter_ajaxRemoveAncillaryService', array('institutionId' => $this->institution->getId(),'imcId' => $this->institutionMedicalCenter->getId() ,'id' => $property->getId() )),
                    'ancillaryServicesData' => $ancillaryServicesData,
                    '_isSelected' => true,
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
     * copy institution anciallary services to medical center
     * Required parameters:
     *     - institutionId
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @author ajacobe
     */
    public function portInstitutionAncillaryServiceAction(Request $request)
    {
        $propertyService = $this->get('services.institution_medical_center_property');
        $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
        
        $assignedServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllServicesByInstitutionMedicalCenter($this->institutionMedicalCenter);
        $institutionAncillaryServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->getAvailableInstitutionServicesByInstitutionMedicalCenter($this->institution, $this->institutionMedicalCenter, $assignedServices);
        
        if($request->get('isCopy')) {
            foreach ($institutionAncillaryServices as $ancillaryService)
            {
                // check if this medical center already have this property value
                if (!$this->get('services.institution_medical_center')->hasPropertyValue($this->institutionMedicalCenter, $propertyType, $ancillaryService['id'])) {
                    
                    $property = $propertyService->createInstitutionMedicalCenterPropertyByName($propertyType->getName(), $this->institution, $this->institutionMedicalCenter);
                    $property->setValue($ancillaryService['id']);
                    try {
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->persist($property);
                        $em->flush();
                
                    }
                    catch (\Exception $e){
                        $response = new Response($e->getMessage(), 500);
                    }                
                }
            }
        }
        
        // get global ancillary services
        $ancillaryServicesData = array(
            'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
            'selectedAncillaryServices' => array()
        );
        foreach ($this->get('services.institution_medical_center_property')->getInstitutionMedicalCenterByPropertyType($this->institutionMedicalCenter, InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE) as $_selectedService) {
            $ancillaryServicesData['currentAncillaryData'][] = array(
                            'id' => $_selectedService->getId(),
                            'value' => $_selectedService->getValue(),
            );
            $ancillaryServicesData['selected'][] = $_selectedService->getValue();
        }
        foreach ($this->get('services.institution_medical_center')->getMedicalCenterServices($this->institutionMedicalCenter) as $_selectedService) {
            $ancillaryServicesData['selectedAncillaryServices'][] = $_selectedService->getId();
        }
        
        $output = array(
                        'html' => $this->renderView('AdminBundle:InstitutionTreatments/Partials:field.ancillaryServices.html.twig', array(
                                        'institution' => $this->institution,
                                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                                        'ancillaryServicesData' => $ancillaryServicesData,
                                        '_isSelected' => true
                        )),
                        'error' => 0
        );
        return $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
    }
    
    public function  showInstitutionAncillaryServiceAction(Request $request)
    {
        $assignedServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllServicesByInstitutionMedicalCenter($this->institutionMedicalCenter);
        $institutionAncillaryServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionProperty')->getAvailableInstitutionServicesByInstitutionMedicalCenter($this->institution, $this->institutionMedicalCenter, $assignedServices);
        $output = array(
                        'html' => $this->renderView('AdminBundle:InstitutionTreatments/Partials:tableList_institution_services.html.twig', array(
                                        'institutionServices' => $institutionAncillaryServices
                        )),
        );
        return $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
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
    
}
