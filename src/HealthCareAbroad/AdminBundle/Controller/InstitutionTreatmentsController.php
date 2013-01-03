<?php
/**
 *
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\DoctorBundle\Form\DoctorFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationSelectorFormType;

use HealthCareAbroad\MediaBundle\Services\MediaService;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;

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
                $response = $this->forward('AdminBundle:InstitutionTreatments:viewMedicalCenter',array('institution' => $this->institution, 'center' => $firstMedicalCenter));
            }
            else {
                // no medical center yet, redirect to add medical center with 
                $this->request->getSession()->setFlash('notice', 'No medical centers yet, add a clinic now.');
                $response = $this->redirect($this->generateUrl('admin_institution_medicalCenter_add', array('institutionId' => $this->institution->getId())));
            }
        }
        else {
            $institutionMedicalCenters = $institutionService->getActiveMedicalCenters($this->institution);
            
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
        $institutionSpecializations = $instSpecializationRepo->getByInstitutionMedicalCenter($this->institutionMedicalCenter);
        $specializationsData = array();
        foreach ($institutionSpecializations as $_institutionSpecialization) {
            $_specialization = $_institutionSpecialization->getSpecialization();
            
            $groupedTreatments = $this->getDoctrine()->getRepository('TreatmentBundle:Treatment')
                ->getBySpecializationId($_specialization->getId(), true);
            if (empty($groupedTreatments)) {
                // there are no  treatments for this medical center
                continue;
            }
            $selectedTreatments = $_institutionSpecialization->getTreatments();
            $_selectedTreamentIds = array();
            foreach ($selectedTreatments as $_treatment) {
                $_selectedTreamentIds[] = $_treatment->getId();
            }
            
            $specializationsData[] = array(
                'institutionSpecialization' => $_institutionSpecialization,
                'groupedTreatments' => $groupedTreatments,
                'selectedTreatments' => $_selectedTreamentIds
            );
        }
        $institutionSpecializationForm = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization(), array('em' => $this->getDoctrine()->getEntityManager()));
        
        $form = $this->createForm(new InstitutionMedicalCenterBusinessHourFormType(),$this->institutionMedicalCenter);
        
        // get global ancillary services
        $ancillaryServicesData = array(
            'globalList' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
            'selectedAncillaryServices' => array()
        );
        
        foreach ($institutionMedicalCenterService->getMedicalCenterServices($this->institutionMedicalCenter) as $_selectedService) {
            $ancillaryServicesData['selectedAncillaryServices'][] = $_selectedService->getId();
        }
        
        $global_awards = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->getInstitutionGlobalAwards($this->institutionMedicalCenter->getId());
        
        $params = array(
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institutionSpecializationsData' => $specializationsData,
            'institutionSpecializationFormName' => InstitutionSpecializationFormType::NAME,
            'institutionSpecializationForm' => $institutionSpecializationForm->createView(),
            'selectedSubMenu' => 'centers',
            'global_awards' => $global_awards,
            'ancillaryServicesData' => $ancillaryServicesData,
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
     *
     * This will add medicalSpecialist on InstitutionMedicalCenter
     */
    public function addMedicalSpecialistAction(Request $request)
    {
        $this->institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
        $doctors = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->getDoctorsByInstitutionMedicalCenter($request->get('imcId'));
        $form = $this->createForm(new \HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType());
        $formActionUrl = $this->generateUrl('admin_institution_medicalCenter_ajaxRemoveAncillaryService', array('institutionId' => $this->institution->getId(), 'imcId' => $request->get('imcId')));
        if ($request->isMethod('POST')) {
    
            $form->bind($request);
            if ($form->isValid() && $form->get('id')->getData()) {
    
                $center = $this->get('services.institution_medical_center')->saveInstitutionMedicalCenterDoctor($form->getData(), $this->institutionMedicalCenter);
                $this->get('session')->setFlash('notice', "Successfully added Medical Specialist");
            }
        }
        $doctorArr = array();
        foreach ($doctors as $each) {
            $doctorArr[] = array('value' => $each['first_name'] ." ". $each['last_name'], 'id' => $each['id'], 'path' => $this->generateUrl('admin_doctor_load_doctor_specializations', array('doctorId' =>  $each['id'])));
        }
        return $this->render('AdminBundle:InstitutionTreatments:add.medicalSpecialist.html.twig', array(
                        'form' => $form->createView(),
                        'institution' => $this->institution,
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'doctorsJSON' => \json_encode($doctorArr)
        ));
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
        
                $center = $this->get('services.institution_medical_center')->saveInstitutionMedicalCenterDoctor(array("firstName" => "", "id" => $doctor->getId()), $this->institutionMedicalCenter);
                
                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_addMedicalSpecialist',array("institutionId" => $this->institution->getId(), "imcId" => $request->get('imcId'))));
            }
        }
        return $this->render('AdminBundle:Doctor:common.form.html.twig', array(
                        'form' => $form->createView(),
                        'institution' => $this->institution,
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'specializations' => $specializations
        ));
    }
    
    public function ajaxRemoveMedicalSpecialistAction(Request $request)
    {
        return $this->render('AdminBundle:InstitutionTreatments:modal.deleteMedicalSpecialist.html.twig');
        
        //         $ancillaryService = $this->getDoctrine()->getRepository('AdminBundle:OfferedService')
        //         ->find($request->get('asId', 0));
    
        //         if (!$ancillaryService) {
        //             throw $this->createNotFoundException('Invalid ancillary service id');
        //         }
    
        //         $propertyService = $this->get('services.institution_medical_center_property');
        //         $propertyType = $propertyService->getAvailablePropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE);
    
        //         // get property value for this ancillary service
        //         $property = $this->get('services.institution_medical_center')->getPropertyValue($this->institutionMedicalCenter, $propertyType, $ancillaryService->getId());
    
        //         try {
        //             $em = $this->getDoctrine()->getEntityManager();
        //             $em->remove($property);
        //             $em->flush();
    
        //             $output = array(
        //                             'html' => $this->renderView('AdminBundle:InstitutionTreatments:row.ancillaryService.html.twig', array(
                        //                                             'institution' => $this->institution,
                        //                                             'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        //                                             'ancillaryService' => $ancillaryService,
                        //                                             '_isSelected' => false
                        //                             )),
                        //                             'error' => 0
                        //             );
        //             $response = new Response(\json_encode($output), 200, array('content-type' => 'application/json'));
        //         }
        //         catch (\Exception $e){
        //             $response = new Response($e->getMessage(), 500);
        //         }
    
        //         return $response;
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
    
                // Get contactNumbers and convert to json format
                $businessHours = json_encode($request->get('businessHours'));
    
                if ($form->isValid()) {
    
                    // Set BusinessHours before saving
                    $form->getData()->setBusinessHours($businessHours);
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
                'selectedSubMenu' => 'centers'
            );
    
            return $this->render('AdminBundle:InstitutionTreatments:form.medicalCenter.html.twig', $params);
        }
    }

    public function editMedicalCenterAction()
    {
        $service = $this->get('services.institution_medical_center');
        $request = $this->request;
        $instSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $specializations = $instSpecializationRepo->getByInstitutionMedicalCenter($this->institutionMedicalCenter);

        $form = $this->createForm(new InstitutionMedicalCenterBusinessHourFormType(),$this->institutionMedicalCenter);
   
        if ($request->isMethod('POST')) {
            $form->bind($request);

            // Get contactNumbers and convert to json format
            if($request->get('businessHours') == null){
                $businessHours = '';
            }else{
               $businessHours = json_encode($request->get('businessHours'));
            }
            // Set BusinessHours before saving
            $form->getData()->setBusinessHours($businessHours);
            $this->institutionMedicalCenter = $service->saveAsDraft($form->getData());

            $request->getSession()->setFlash('success', '"' . $this->institutionMedicalCenter->getName() . '"' . " has been updated. You can now add Specializations to this center.");

            // TODO: Verify Event!
            // dispatch event
            $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER,
                $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER, $this->institutionMedicalCenter, array('institutionId' => $this->institution->getId())
            ));

            // redirect to step 2;
            return $this->redirect($this->generateUrl('admin_institution_medicalCenter_addSpecialization',array(
                'institutionId' => $this->institution->getId(),
                'imcId' => $this->institutionMedicalCenter->getId()
            )));
        }

        $params = array(
            'form' => $form->createView(),
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter
        );

        return $this->render('AdminBundle:InstitutionTreatments:form.medicalCenter.html.twig', $params);
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
    public function updateMedicalCenterStatusAction()
    {
        $request = $this->getRequest();
        $status = $request->get('status');

        $redirectUrl = $this->generateUrl('admin_institution_manageCenters', array('institutionId' => $request->get('institutionId')));

        if(!InstitutionMedicalCenterStatus::isValid($status)) {
            $request->getSession()->setFlash('error', "Unable to update status. $status is invalid status value!");

            return $this->redirect($redirectUrl);
        }

        $this->institutionMedicalCenter->setStatus($status);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institutionMedicalCenter);
        $em->flush($this->institutionMedicalCenter);

        // dispatch EDIT institutionMedicalCenter event
        $actionEvent = InstitutionBundleEvents::ON_UPDATE_STATUS_INSTITUTION_MEDICAL_CENTER;
        $event = $this->get('events.factory')->create($actionEvent, $this->institutionMedicalCenter, array('institutionId' => $request->get('institutionId')));
        $this->get('event_dispatcher')->dispatch($actionEvent, $event);

        $request->getSession()->setFlash('success', '"'.$this->institutionMedicalCenter->getName().'" status has been updated!');


        return $this->redirect($redirectUrl);
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
     * Remove a Treatmnent from an institution specialization
     * Expected parameters
     *     
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxRemoveSpecializationTreatmentAction(Request $request)
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
        
        try {
            $institutionSpecialization->removeTreatment($treatment);
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionSpecialization);
            $em->flush();
            
            $output = array(
                'link' => array(
                    'href' => $this->generateUrl('admin_institution_medicalCenter_ajaxAddSpecializationTreatment', array(
                        'tId' => $treatment->getId(),
                        'institutionId' => $this->institution->getId(),
                        'isId' =>  $institutionSpecialization->getId())
                    ),
                    'html' => 'Add Treatment'
                ),
                'icon' => 'icon-ok'
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
    public function addSpecializationAction()
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
            $specializations = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->getActiveSpecializations();
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
}