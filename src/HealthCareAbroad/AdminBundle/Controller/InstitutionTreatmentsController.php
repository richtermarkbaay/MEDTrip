<?php
/**
 *
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\AdminBundle\Controller;

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
        $criteria = array('institution' => $this->institution);
        $institutionMedicalCenters = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findBy($criteria);

        $params = array(
            'institution' => $this->institution,
            'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
            'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions(),
            'institutionMedicalCenters' => $institutionMedicalCenters,
            'pager' => $this->pager,
            'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution)
        );

         return $this->render('AdminBundle:InstitutionTreatments:viewAllMedicalCenters.html.twig', $params);
    }

    /**
     * Actionn handler for viewing all InstitutionMedicalCenter of selected institution
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewMedicalCenterAction()
    {
        $instSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
        $specializations = $instSpecializationRepo->getByInstitutionMedicalCenter($this->institutionMedicalCenter);

        $form = $this->createForm(new InstitutionMedicalCenterBusinessHourFormType(),$this->institutionMedicalCenter);
        $global_awards = $this->getDoctrine()->getRepository('HelperBundle:GlobalAward')->getInstitutionGlobalAwards($this->institutionMedicalCenter->getId());
        $ancilliaryServices = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenterProperty')->getAllServicesByInstitutionMedicalCenter($this->institutionMedicalCenter->getId(), $this->institution->getId());
        $params = array(
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'specializations' => $specializations,
            'selectedSubMenu' => 'centers',
            'form' => $form->createView(),
            'global_awards' => $global_awards,
            'services' => $ancilliaryServices,
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
        $formActionUrl = $this->generateUrl('admin_institution_medicalCenter_addAncilliaryService', array('institutionId' => $this->institution->getId(), 'imcId' => $request->get('imcId')));
        if ($request->isMethod('POST')) {
    
            $form->bind($request);
            if ($form->isValid() && $form->get('id')->getData()) {
    
                $center = $this->get('services.institution_medical_center')->saveInstitutionMedicalCenterDoctor($form->getData(), $this->institutionMedicalCenter);
                $this->get('session')->setFlash('notice', "Successfully added Medical Specialist");
            }
        }
        $doctorArr = array();
        foreach ($doctors as $each) {
            $doctorArr[] = array('value' => $each['first_name'] ." ". $each['last_name'], 'id' => $each['id'], 'path' => $this->generateUrl('institution_load_doctor_specializations', array('doctorId' =>  $each['id'])));
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
    
    private function saveMedia($fileBag)
    {
        if($fileBag['media']) {
            $media = $this->get('services.media')->uploadDoctorImage($fileBag['media']);
            return $media;
        }
    
        return null;
    }
    
    public function addMedicalCenterOfferedServiceAction(Request $request)
    {
        $center = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
        $form = $this->get('services.institution_medical_center_property.formFactory')->buildFormByInstitutionMedicalCenterPropertyTypeName($this->institution, $center, 'ancilliary_service_id');
   	    $formActionUrl = $this->generateUrl('admin_institution_medicalCenter_addNewMedicalSpecialist', array('institutionId' => $this->institution->getId(), 'imcId' => $request->get('imcId')));
   	    if ($request->isMethod('POST')) {
   	        $form->bind($request);
   	        if ($form->isValid()) {
   	            $this->get('services.institution_property')->save($form->getData());
   	    
   	            return $this->redirect($formActionUrl);
   	        }
   	    }
   	    
   	    $params = array(
   	                    'formAction' => $formActionUrl,
   	                    'form' => $form->createView()
   	    );
   	    return $this->render('AdminBundle:InstitutionMedicalCenterProperties:common.form.html.twig', $params);
    }
    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMedicalCenterAction()
    {
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
            $businessHours = json_encode($request->get('businessHours'));

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


    /**
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
                $response = $this->redirect($this->generateUrl('admin_institution_medicalCenter_view', array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId())));
            }
            else{
                $response = $this->redirect($this->generateUrl('admin_institution_manageCenters', array('institutionId' => $this->institution->getId())));
            }
            
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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editSpecializationAction()
    {
        $institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($this->request->get('isId'));
        $institutionTreatments = $institutionSpecialization->getTreatments();
        $institutionTreatmentIds = array();
        foreach($institutionTreatments as $treatment) {
            $institutionTreatmentIds[] = $treatment->getId();
        }

        $form = $this->createForm(new InstitutionSpecializationFormType(), $institutionSpecialization, array('em' => $this->getDoctrine()->getEntityManager()));

        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);

            if ($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($form->getData());
                $em->flush();

                if($institutionSpecialization->getId()) {
                    $treatmentIds = $this->request->get('treatments', array());
                    $deleteTreatmentsIds = array_diff($institutionTreatmentIds, $treatmentIds);

                    $instSpecializationRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization');
                    $instSpecializationRepo->updateTreatments($institutionSpecialization->getId(), $treatmentIds, $deleteTreatmentsIds);
                }

                $this->request->getSession()->setFlash('success', "Specialization has been saved!");

                // TODO: fire event

                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_editSpecialization',array(
                    'institutionId' => $this->institution->getId(),
                    'isId' => $institutionSpecialization->getId()
                )));
            }
        }

        $params = array(
            'form' => $form->createView(),
            'institutionSpecialization' => $institutionSpecialization,
            'institutionId' => $this->institution->getId(),
            'institutionTreatmentIds' => $institutionTreatmentIds
        );

        return $this->render('AdminBundle:InstitutionTreatments:editSpecialization.html.twig', $params);
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

}