<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

use Doctrine\ORM\AbstractQuery;

use HealthCareAbroad\DoctorBundle\Entity\MedicalSpeciality;

use Doctrine\ORM\Query;

use Symfony\Component\EventDispatcher\GenericEvent;

use HealthCareAbroad\MailerBundle\Event\MailerBundleEvents;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterDoctorFormType;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use Mapping\Fixture\Xml\Status;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @var Institution
     */
    //private $institution = null;

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

        // Temporary condition for eagerLoad
        if ($imcId = $this->getRequest()->get('imcId',0)){
            if($this->getRequest()->attributes->get('_route') == 'institution_medicalCenter_view') {
                $this->institutionMedicalCenter = $this->service->findById($imcId, array('logo' => 'a.logo','contactDetails' => 'a.contactDetails', 'businessHours' => 'a.businessHours', 'specializations' => 'a.institutionSpecializations'));

                if ($this->institutionMedicalCenter->getInstitution()->getId() != $this->institution->getId() ) {
                    return $this->_redirectIndexWithFlashMessage('Invalid medical center.', 'error');
                }
            } else {
                $this->institutionMedicalCenter  = $this->repository->find($imcId);
            }

            if (!$this->institutionMedicalCenter) {
                if ($this->getRequest()->isXmlHttpRequest()) {
                    throw $this->createNotFoundException('Invalid medical center.');
                }
                else {
                    return $this->_redirectIndexWithFlashMessage('Invalid medical center.', 'error');
                }
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

        // Add Medical Center Form
        $institutionMedicalCenter = new InstitutionMedicalCenter();
        $institutionMedicalCenter->setInstitution($this->institution);
        
        $this->get('services.contact_detail')->initializeContactDetails($institutionMedicalCenter, array(ContactDetailTypes::PHONE), $this->institution->getCountry());
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
        $this->get('services.contact_detail')->initializeContactDetails($this->institutionMedicalCenter, array(ContactDetailTypes::PHONE), $this->institution->getCountry());

        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false));

        $currentGlobalAwards = $this->get('services.institution_medical_center_property')->getGlobalAwardPropertiesByInstitutionMedicalCenter($this->institutionMedicalCenter);
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());

        // Doctors
        $editDoctor = $doctor = new Doctor();
        $doctor->addInstitutionMedicalCenter($this->institutionMedicalCenter);
        $doctorForm = $this->createForm(new InstitutionMedicalCenterDoctorFormType(), $doctor);
        $doctors = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->findByInstitutionMedicalCenter($this->institutionMedicalCenter->getId(), Query::HYDRATE_OBJECT);

        if(!empty($doctors)) {
            $editDoctor = $doctors[0];
        }
        $this->get('services.contact_detail')->initializeContactDetails($editDoctor, array(ContactDetailTypes::PHONE), $this->institution->getCountry());
        $editDoctorForm = $this->createForm(new InstitutionMedicalCenterDoctorFormType('editInstitutionMedicalCenterDoctorForm'), $editDoctor);


        return $this->render('InstitutionBundle:MedicalCenter:view.html.twig', array(
            'institutionMedicalCenterForm' => $form->createView(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter,

            'medicalCenterPhotos' => $this->get('services.institution.gallery')->getInstitutionMedicalCenterPhotos($this->institutionMedicalCenter->getId()),
            'specializations' => $this->institutionMedicalCenter->getInstitutionSpecializations(),
            'ancillaryServicesData' =>  $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
            'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView(),
            'currentGlobalAwards' => $currentGlobalAwards,
            'editGlobalAwardForm' => $editGlobalAwardForm->createView(),
            'doctors' => $this->get('services.doctor')->doctorsObjectToArray($doctors),
            'doctorForm' => $doctorForm->createView(),
            'editDoctorForm' => $editDoctorForm->createView()
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
                $this->get('services.contact_detail')->initializeContactDetails($this->institutionMedicalCenter, array(ContactDetailTypes::PHONE));
                $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution),$this->institutionMedicalCenter, array(
                    InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false,
                    InstitutionMedicalCenterFormType::OPTION_REMOVED_FIELDS => $removedFields
                ));
                $form->bind($request);
    
                if ($form->isValid()) {
                    $institutionMedicalCenterService = $this->get('services.institution_medical_center');
                    $this->get('services.contact_detail')->removeInvalidContactDetails($this->institutionMedicalCenter);
    
                    $responseContent = array();
                    if (isset($formVariables['businessHours'])) {
                        $institutionMedicalCenterService->clearBusinessHours($this->institutionMedicalCenter);
                        foreach ($this->institutionMedicalCenter->getBusinessHours() as $hour ) {
                            $hour->setInstitutionMedicalCenter($this->institutionMedicalCenter);
                        }
                        $institutionMedicalCenterService->save($this->institutionMedicalCenter);
                        $responseContent = array('businessHours' => $formVariables['businessHours']);
                    } elseif (isset($form['services'])) {
                        $propertyService->addPropertyForInstitutionMedicalCenterByType($this->institution, $form['services']->getData(), InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE, $this->institutionMedicalCenter);
                        $responseContent = array('services' => $form['services']);

                    } elseif ($awardType = $request->get('awardTypeKey')) {

                        $awardsData = isset($form['awards']) ? $form['awards']->getData() : array();

                        $propertyService->addPropertyForInstitutionMedicalCenterByType($this->institution, $awardsData, InstitutionPropertyType::TYPE_GLOBAL_AWARD, $this->institutionMedicalCenter);
                        $globalAwards = $propertyService->getGlobalAwardPropertiesByInstitutionMedicalCenter($this->institutionMedicalCenter);

                        $responseContent['awardsType'] = $awardType;
                        $responseContent['awardsHtml'] = $this->renderView('InstitutionBundle:Widgets/Profile:globalAwards.html.twig', array(
                            'eachAward' => array('list' => $globalAwards[$awardType]),
                            'type' => $awardType.'s',
                            'toggleBtnId' => 'clinic-edit-'.$awardType.'s-btn'
                        ));
                    } else {    
                        $institutionMedicalCenterService->save($this->institutionMedicalCenter);

                        if ($this->isSingleCenter) {
                            // also update the instituion name and description
                            $this->institution->setName($this->institutionMedicalCenter->getName());
                            $this->institution->setDescription($this->institutionMedicalCenter->getDescription());
                            $this->get('services.institution.factory')->save($this->institution);
                        }

                        if(isset($formVariables['address'])) {

                            $formVariables['stringAddress'] = $this->get('services.institutionMedicalCenter.twig.extension')->getCompleteAddressAsString($this->institutionMedicalCenter);
                        }

                        if(isset($formVariables['contactDetails'])) {
                            $formVariables['contactDetails'] = $this->get('services.contact_detail')->getContactDetailsStringValue($this->institutionMedicalCenter->getContactDetails());
                        }

                        $responseContent = array('institutionMedicalCenter' => $formVariables);
                    }
                    
                    // Invalidate InstitutionMedicalCenterProfile memcache
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

                    // Invalidate InstitutionProfile memcache
                    $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));

                    $response = new Response(\json_encode($responseContent), 200, array('content-type' => 'application/json'));

                } else {
                    $errors = array();
                    foreach ($form->getChildren() as $field){
                        if (\count($eachErrors = $field->getErrors())){
                            $errors[] = array('field' => $field->getName(), 'error' => $eachErrors[0]->getMessageTemplate());
                        }
                    }

                    $response = new Response(\json_encode(array('errors' => $errors)), 400, array('content-type' => 'application/json'));

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

            // Invalidate InstitutionMedicalCenterProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

            return new Response(\json_encode(true),200, array('content-type' => 'application/json'));
        }
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

            $this->get('services.contact_detail')->initializeContactDetails($this->institutionMedicalCenter, array(ContactDetailTypes::PHONE), $this->institution->getCountry());
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
                $this->get('services.contact_detail')->removeInvalidContactDetails($this->institutionMedicalCenter);
                $this->institutionMedicalCenter = $this->get('services.institutionMedicalCenter')->saveAsDraft($form->getData());
                $output =  $this->generateUrl('institution_medicalCenter_view', array('imcId' => $this->institutionMedicalCenter->getId()));

                $this->get('event_dispatcher')->dispatch(
                    MailerBundleEvents::NOTIFICATIONS_CLINIC_CREATED,
                    new GenericEvent($this->institutionMedicalCenter, array('userEmail' => $request->getSession()->get('userEmail'))));

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

    /**
     * Modified for new markup in adding specialist in clinic profile doctors tab
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addDoctorAction(Request $request)
    {
        $doctor = new Doctor();
        $doctor->addInstitutionMedicalCenter($this->institutionMedicalCenter);
        $form = $this->createForm(new InstitutionMedicalCenterDoctorFormType(), $doctor);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $doctor = $form->getData();
                $doctor->setStatus(Doctor::STATUS_ACTIVE);

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($doctor);
                $em->flush($doctor);

                // Invalidate InstitutionMedicalCenterProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

                // Invalidate InstitutionProfile memcache
                $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));

                $data = array(
                    'status' => true,
                    'message' => 'Doctor has been added to your clinic!',
                    'doctor' => $this->get('services.doctor')->toArrayDoctor($doctor),
                    'editDoctorUrl' => $this->generateUrl('institution_medicalCenter_ajaxUpdateDoctor', array('imcId' => $this->institutionMedicalCenter->getId(), 'doctorId' => $doctor->getId())),
                    'removeDoctorUrl' => $this->generateUrl('institution_medicalCenter_removeDoctor', array('imcId' => $this->institutionMedicalCenter->getId(), 'doctorId' => $doctor->getId())),
                    'uploadLogoUrl' => $this->generateUrl('institution_doctor_logo_upload', array('imcId' => $this->institutionMedicalCenter->getId(), 'doctorId' => $doctor->getId()))
                );
            } else {
                $data = array('status' => false, 'message' => $form->getErrorsAsString());
            }
        }

        return new Response(json_encode($data), 200, array('Content-Type'=>'application/json'));
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

        try {
            $this->institutionMedicalCenter->addDoctor($doctor);
            $this->service->save($this->institutionMedicalCenter);

            $result['status'] = true;
            $result['doctor'] = $this->get('services.doctor')->toArrayDoctor($doctor);
            $result['editDoctorUrl'] = $this->generateUrl('institution_medicalCenter_ajaxUpdateDoctor', array('imcId' => $this->institutionMedicalCenter->getId(), 'doctorId' => $doctor->getId()));
            $result['removeDoctorUrl'] = $this->generateUrl('institution_medicalCenter_removeDoctor', array('imcId' => $this->institutionMedicalCenter->getId(), 'doctorId' => $doctor->getId()));
            $result['uploadLogoUrl'] = $this->generateUrl('institution_doctor_logo_upload', array('imcId' => $this->institutionMedicalCenter->getId(), 'doctorId' => $doctor->getId()));

            // Invalidate InstitutionMedicalCenterProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));
            
            // Invalidate InstitutionProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));

        } catch (\Exception $e) {}

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
        $this->get('services.contact_detail')->initializeContactDetails($doctor, array(ContactDetailTypes::PHONE));

        $form = $this->createForm(new InstitutionMedicalCenterDoctorFormType('editInstitutionMedicalCenterDoctorForm'), $doctor);
        $form->bind($request);

        if ($form->isValid()) {
            $this->get('services.contact_detail')->removeInvalidContactDetails($doctor);

            if($medicalSpecialitiesIds = $request->get('doctor_medical_specialities', array())) {
                $qb = $this->getDoctrine()->getEntityManagerForClass('DoctorBundle:MedicalSpeciality')->createQueryBuilder();
                $qb->select('a')->from('DoctorBundle:MedicalSpeciality', 'a')
                   ->where($qb->expr()->in('a.id', ':medicalSpecialitiesIds'))
                   ->setParameter(':medicalSpecialitiesIds', $medicalSpecialitiesIds);

                $medicalSpecialities = $qb->getQuery()->getResult();

                // Add selected medicalSpecialities
                foreach ($medicalSpecialities as $each) {
                    if(!$doctor->getMedicalSpecialities()->contains($each)) {
                        $doctor->addMedicalSpeciality($each);                        
                    }
                }
            }

            // Remove non-selected medicalSpecialities
            foreach($doctor->getMedicalSpecialities() as $each) {
                if(!in_array($each->getId(), $medicalSpecialitiesIds)) {
                    $doctor->removeMedicalSpeciality($each);
                }
            }

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($doctor);
            $em->flush();

            // Invalidate InstitutionMedicalCenterProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));
            
            // Invalidate InstitutionProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));

            $data = array(
                'status' => true,
                'message' => 'Doctor info has been updated!',
                'doctor' => $this->get('services.doctor')->toArrayDoctor($doctor)
            );
            
        } else {
            $data = array('status' => false, 'message' => $form->getErrorsAsString());
        }

        return new Response(\json_encode($data),200, array('content-type' => 'application/json'));
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

            // Invalidate InstitutionMedicalCenterProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

            // Invalidate InstitutionProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));
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
     * Upload logo for Institution Medical Center
     * @param Request $request
     */
    public function uploadAction(Request $request)
    {
        if ($request->files->get('logo')) {
            $file = $request->files->get('logo');
            $media = $this->get('services.institution.media')->medicalCenterUploadLogo($file, $this->institutionMedicalCenter);

            if($media->getName()) {
                $src = $this->get('services.institution')->mediaTwigExtension->getInstitutionMediaSrc($media->getName(), ImageSizes::MEDIUM);
                $data['mediaSrc'] = $src;
            }
            $data['status'] = true;

            // Invalidate InstitutionMedicalCenterProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionMedicalCenterProfileKey($this->institutionMedicalCenter->getId()));

            // Invalidate InstitutionProfile memcache
            $this->get('services.memcache')->delete(FrontendMemcacheKeysHelper::generateInsitutionProfileKey($this->institutionMedicalCenter->getInstitution()->getId()));
        }

        return new Response(\json_encode($data), 200, array('content-type' => 'application/json'));
    }
}