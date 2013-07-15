<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterDoctorFormType;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use Mapping\Fixture\Xml\Status;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
                $this->institutionMedicalCenter = $this->service->findById($imcId, false);

                // medical center group does not belong to this institution
                if ($this->institutionMedicalCenter->getInstitution()->getId() != $this->institution->getId()) {
                    return $this->_redirectIndexWithFlashMessage('Invalid medical center.', 'error');
                }
            } else {
                $this->institutionMedicalCenter  = $this->repository->find($imcId);
            }
        }

        // non-existent medical center group
        if (!$this->institutionMedicalCenter) {
            if ($this->getRequest()->isXmlHttpRequest()) {
                throw $this->createNotFoundException('Invalid medical center.');
            }
            else {
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
        $this->get('services.contact_detail')->initializeContactDetails($this->institutionMedicalCenter, array(ContactDetailTypes::PHONE));

        $doctor = new Doctor();
        $doctor->addInstitutionMedicalCenter($this->institutionMedicalCenter);
        $doctorForm = $this->createForm(new InstitutionMedicalCenterDoctorFormType(), $doctor);

        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false));

        $currentGlobalAwards = $this->get('services.institution_medical_center_property')->getGlobalAwardPropertiesByInstitutionMedicalCenter($this->institutionMedicalCenter);
        $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());

        $editDoctor = new Doctor();
        if($this->institutionMedicalCenter->getDoctors()->count()) {
            $editDoctor = $this->institutionMedicalCenter->getDoctors()->first();
        }
        $this->get('services.contact_detail')->initializeContactDetails($editDoctor, array(ContactDetailTypes::PHONE));

        $editForm = $this->createForm(new InstitutionMedicalCenterDoctorFormType('editInstitutionMedicalCenterDoctorForm'), $editDoctor);
        return $this->render('InstitutionBundle:MedicalCenter:view.html.twig', array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institutionMedicalCenterForm' => $form->createView(),

            'specializations' => $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->getActiveSpecializationsByInstitutionMedicalCenter($this->institutionMedicalCenter),
            'ancillaryServicesData' =>  $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
            'commonDeleteForm' => $this->createForm(new CommonDeleteFormType())->createView(),
            'currentGlobalAwards' => $currentGlobalAwards,
            'editGlobalAwardForm' => $editGlobalAwardForm->createView(),

            'doctors' =>  $this->get('services.doctor')->doctorsObjectToArray($this->institutionMedicalCenter->getDoctors()),
            'doctorForm' => $doctorForm->createView(),
            'editForm' => $editForm->createView()
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
                    if (isset($formVariables['businessHours'])) {
                        $institutionMedicalCenterService->clearBusinessHours($this->institutionMedicalCenter);
                        foreach ($this->institutionMedicalCenter->getBusinessHours() as $_hour ) {
                            $_hour->setInstitutionMedicalCenter($this->institutionMedicalCenter );
                        }
                    }
                    $this->get('services.contact_detail')->removeInvalidContactDetails($this->institutionMedicalCenter);
                    $institutionMedicalCenterService->save($this->institutionMedicalCenter);

                    if(!empty($form['services']))
                    {
                        $propertyService->removeInstitutionMedicalCenterPropertiesByPropertyType(InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE, $this->institutionMedicalCenter);
                        $propertyService->addPropertyForInstitutionMedicalCenterByType($this->institution, $form['services']->getData(),InstitutionPropertyType::TYPE_ANCILLIARY_SERVICE, $this->institutionMedicalCenter);

                    }if(!empty($form['awards']))
                    {
//                         $propertyService->removeInstitutionMedicalCenterPropertiesByPropertyType(InstitutionPropertyType::TYPE_GLOBAL_AWARD, $this->institutionMedicalCenter);
                        $propertyService->addPropertyForInstitutionMedicalCenterByType($this->institution, $form['awards']->getData(),InstitutionPropertyType::TYPE_GLOBAL_AWARD, $this->institutionMedicalCenter);
                    }

                    if ($this->isSingleCenter) {
                        // also update the instituion name and description
                        $this->institution->setName($this->institutionMedicalCenter->getName());
                        $this->institution->setDescription($this->institutionMedicalCenter->getDescription());
                        $this->get('services.institution.factory')->save($this->institution);
                    }

                    $output['institutionMedicalCenter'] = array();
                    foreach ($formVariables as $key => $v){

                        if($key == 'services')
                        {
                            $html = $this->renderView('InstitutionBundle:Widgets/Profile:services.html.twig', array(
                                'institutionMedicalCenter' => $this->institutionMedicalCenter,
                                'ancillaryServicesData' => $this->get('services.helper.ancillary_service')->getActiveAncillaryServices(),
                            ));

                            return new Response(\json_encode(array('html' => $html)), 200, array('content-type' => 'application/json'));

                        }if($key == 'awards')
                        {
                            $html = array();
                            $typeKey = $request->get('awardTypeKey');
                            $editGlobalAwardForm = $this->createForm(new InstitutionGlobalAwardFormType());
                            $globalAwards = $propertyService->getGlobalAwardPropertiesByInstitutionMedicalCenter($this->institutionMedicalCenter);
                            foreach($globalAwards as $key => $global ) {
                                $html['html'][$key][] = $this->renderView('InstitutionBundle:Widgets/Profile:globalAwards.html.twig', array(
                                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                                        'editGlobalAwardForm' => $editGlobalAwardForm->createView(),
                                        'eachAward' => array('list' => $global),
                                        'type' => $key
                                ));
                                
                            }
                            return new Response(\json_encode($html), 200, array('content-type' => 'application/json'));
                        }
                         if($key == 'contactDetails' ){
                             $value = $this->get('services.contact_detail')->getContactDetailsStringValue($this->institutionMedicalCenter->{'get'.$key}());
                             $output['institutionMedicalCenter'][$key]['phoneNumber'] = $value;
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

                            if( $key == 'socialMediaSites') {
                                $value = json_decode($value, true);
                            }
                            $output['institutionMedicalCenter'][$key] = $value;
                        }
                    }

                    $output['form_error'] = 0;

                    /*
                     * TODO: Needs to change the validation for awards and services
                    * Always expects empty if form submitted are from awards or services
                    */
                    if(empty($output['institutionMedicalCenter'])){
                        $errors = array('error' => 'Please select at least one.');
                        return new Response(\json_encode(array('html' => $errors)), 400, array('content-type' => 'application/json'));
                    }

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

            $this->get('services.contact_detail')->initializeContactDetails($this->institutionMedicalCenter, array(ContactDetailTypes::PHONE));

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

                // TODO: Update this when we have formulated a strategy for our events system
                // We can't use InstitutionBundleEvents; we don't know the consequences of the event firing up other listeners.
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
     * NOTE: This is an AJAX request
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
        $hasTreatments = false;

        if(!$hasTreatments) {
            $errors = 'Please select at least one specialization.';
        }

        // if AJAX request
        if ($request->isXmlHttpRequest()) {
            if ($errors) {
                $response = new Response($errors ,400);
            }
            else {
                $response = new Response(\json_encode($ajaxOutput),200, array('content-type' => 'application/json'));
            }
        }
        return $response;
    }

    private function saveSpecializationsAndTreatments($submittedSpecializations)
    {

        foreach ($submittedSpecializations as $specializationId => $data) {

            if(isset($data['treatments']) && count($data['treatments'])) {
                $specialization = $this->get('services.treatment_bundle')->getSpecialization($specializationId);
                $institutionSpecialization = new InstitutionSpecialization();
                $institutionSpecialization->setSpecialization($specialization);
                $institutionSpecialization->setInstitutionMedicalCenter($this->institutionMedicalCenter);
                $institutionSpecialization->setStatus(InstitutionSpecialization::STATUS_ACTIVE);
                $institutionSpecialization->setDescription('');

                // set passed treatments as choices
                $defaultChoices = array();
                $treatmentChoices = $this->get('services.treatment_bundle')->findTreatmentsByIds($data['treatments']);
                foreach ($treatmentChoices as $treatment) {
                    $defaultChoices[$treatment->getId()] = $treatment->getName();
                    // add the treatment
                    $institutionSpecialization->addTreatment($treatment);
                }

                $form = $this->createForm(new InstitutionSpecializationFormType(), $institutionSpecialization, array('default_choices' => $defaultChoices));
                $form->bind($data);
                if ($form->isValid()) {
                    $hasTreatments = true;
                    $em->persist($institutionSpecialization);
                    $em->flush();
                    if ($request->isXmlHttpRequest()) {
                        $ajaxOutput['html'][] = $this->renderView('InstitutionBundle:MedicalCenter:listItem.institutionSpecializationTreatments.html.twig', array(
                            'each' => $_institutionSpecialization,
                            'institutionMedicalCenter' => $this->institutionMedicalCenter,
                            'commonDeleteForm' => $commonDeleteForm->createView()
                        ));
                    }
                }
            }
        }
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
            $fileBag = $request->files->get($form->getName());

            if(isset($fileBag['media'])) {
                $this->get('services.doctor.media')->uploadLogo($fileBag['media'], $doctor);
            }

            $this->get('services.contact_detail')->removeInvalidContactDetails($doctor);

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($doctor);
            $em->flush();

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
     * @deprecated
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

                $responseContent = array('id' => $_id);
                $response = new Response(\json_encode($responseContent), 200, array('content-type' => 'application/json'));
            }
            else {
                $response = new Response("Invalid form", 400);
            }
        }

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

                        } catch (\Exception $e) {
                            $errors[] = $e->getMessage();
                        }
                    }
                    else {
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
            $media = $this->get('services.institution.media')->medicalCenterUploadLogo($file, $this->institutionMedicalCenter);

            if($media->getName()) {
                $src = $this->get('services.institution')->mediaTwigExtension->getInstitutionMediaSrc($media->getName(), ImageSizes::MEDIUM);
                $data['mediaSrc'] = $src;
            }
            $data['status'] = true;
        }

        return new Response(\json_encode($data), 200, array('content-type' => 'application/json'));
        //return $this->redirect($this->getRequest()->headers->get('referer'));
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