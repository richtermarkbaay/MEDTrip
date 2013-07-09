<?php
/*
 * @author Alnie Jacobe
 */

namespace HealthCareAbroad\InstitutionBundle\Controller;


use Symfony\Component\EventDispatcher\GenericEvent;

use HealthCareAbroad\MailerBundle\Event\MailerBundleEvents;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;


use HealthCareAbroad\MediaBundle\Services\ImageSizes;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserSignUpFormType;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use Symfony\Component\HttpKernel\Exception\HttpException;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterFormType;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterDoctorFormType;

use HealthCareAbroad\InstitutionBundle\Services\SignUpService;

use HealthCareAbroad\InstitutionBundle\Entity\SignUpStep;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionProfileFormType;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSignUpFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionInvitationEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionInvitationEvents;
use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvents;


use HealthCareAbroad\InstitutionBundle\Form\InstitutionInvitationType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation;
use HealthCareAbroad\HelperBundle\Entity\InvitationToken;

use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\HelperBundle\Services\LocationService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;

class InstitutionSignUpController extends InstitutionAwareController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var SignUpStep
     */
    private $currentSignUpStep;

    /**
     *
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;

    /**
     * @var InstitutionService
     */
    private $institutionService;

    /**
     * @var SignUpService
     */
    private $signUpService;

    public function preExecute()
    {
        $this->signUpService = $this->get('services.institution_signup');
        $this->institutionService = $this->get('services.institution');
        $this->request = $this->getRequest();

        if ($imcId = $this->getRequest()->get('imcId', 0)) {
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($imcId,false);

        }

        parent::preExecute();
    }

    private function _updateInstitutionSignUpStepStatus(SignUpStep $step, $flushObject = false)
    {
        $this->getRequest()->getSession()->set('institutionSignupStepStatus', $step->getStepNumber());
        $this->institution->setSignupStepStatus($step->getStepNumber());

        if($flushObject) {
            $this->get('services.institution.factory')->save($this->institution);
        }
    }

    /**
     * TODO: THIS IS MISPLACED
     * invite institutions
     */
    public function inviteAction()
    {
        $invitation = new InstitutionInvitation();
        $form = $this->createForm(new InstitutionInvitationType(), $invitation);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {

            $form->bindRequest($request);
            if ($form->isValid()) {

                //send institution invitation
                $sendingResult = $this->get('services.invitation')->sendInstitutionInvitation($invitation);

                $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_INVITATION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION_INVITATION, $invitation));
                $this->get('session')->setFlash('success', "Invitation sent to ".$invitation->getEmail());
            }
        }

        return $this->render('InstitutionBundle:Token:create.html.twig', array(
                'form' => $form->createView()
        ));
    }

    /**
     * Sign up page handler
     *
     * @param Request $request
     */
    public function signUpAction(Request $request)
    {
        $error_message = '';
        $success = false;
        // checking for security context here does not work since this is not firewalled
        // TODO: find a better approach
//         if ($this->get('session')->get('institutionId')) {
            // redirect to dashboard if there is an active session
            //return $this->redirect($this->generateUrl('institution_homepage'));
//         }
        $factory = $this->get('services.institution.factory');
        $institution = $factory->createInstance();
        $institutionUser = new InstitutionUser();

        $this->get('services.contact_detail')->initializeContactDetails($institutionUser, array(ContactDetailTypes::PHONE, ContactDetailTypes::MOBILE));
        $form = $this->createForm(new InstitutionUserSignUpFormType(), $institutionUser);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $postData = $request->get('institutionUserSignUp');
                $institutionUser = $form->getData();
                // initialize required database fields
                $institution->setName(uniqid());
                $institution->setAddress1('');
                $institution->setContactEmail('');
                $institution->setContactNumber('');
                $institution->setDescription('');
                $institution->setCoordinates('');
                $institution->setType(trim($postData['type'])); /* FIX ME! */
                $institution->setState('');
                $institution->setWebsites('');
                $institution->setStatus(InstitutionStatus::getBitValueForInactiveStatus());
                $institution->setZipCode('');
                $institution->setSignupStepStatus(1); // this is always the first step
                $factory->save($institution);

                // create Institution user
                $institutionUser->setEmail($form->get('email')->getData());
                $institutionUser->setFirstName($form->get('firstName')->getData());
                $institutionUser->setLastName($form->get('lastName')->getData());
                $institutionUser->setContactNumber('');
                $institutionUser->setPassword($form->get('password')->getData());
                $institutionUser->setJobTitle($form->get('jobTitle')->getData());
                $institutionUser->setInstitution($institution);
                $institutionUser->setStatus(SiteUser::STATUS_ACTIVE);
                $this->get('services.contact_detail')->removeInvalidContactDetails($institutionUser);
                // dispatch event
                $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION,
                    $this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION,$institution,array('institutionUser' => $institutionUser)
                ));

                // auto login
                $institutionUserService = $this->get('services.institution_user');
                $roles = $institutionUserService->getUserRolesForSecurityToken($institutionUser);
                $securityToken = new UsernamePasswordToken($institutionUser,$institutionUser->getPassword() , 'institution_secured_area', $roles);
                $this->get('session')->set('_security_institution_secured_area',  \serialize($securityToken));
                $this->get('security.context')->setToken($securityToken);
                $institutionUserService->setSessionVariables($institutionUser);

                // commented out due to duplicate messages
                $request->getSession()->setFlash('callout', "");
                $request->getSession()->setFlash('success', "<b>Congratulations!</b> You have successfully setup your personal account."); //set flash message
                return $this->redirect($this->generateUrl('institution_signup_setup_profile'));
            }
            $form_errors = $this->get('validator')->validate($form);
            if($form_errors){
                    $error_message = 'We need you to correct some of your input. Please check the fields in red.';
            }
        }

        return $this->render('InstitutionBundle:SignUp:signUp.html.twig', array(
            'form' => $form->createView(),
            'institutionTypes' => InstitutionTypes::getFormChoices(),
            'error_message' => $error_message,
        ));
    }

    /**
     * Landing page after signing up as an Institution. Logic will differ depending on the type of institution
     *
     * @param Request $request
     */
    public function setupProfileAction(Request $request)
    {
        //reset for in InstitutionSignUpController signUpAction() this will be temporarily set to uniqid() as a workaround for slug error
        $this->institution->setName('');

        switch ($this->institution->getType())
        {
            case InstitutionTypes::SINGLE_CENTER:

                // get the current step by this route
                $this->currentSignUpStep = $this->signUpService->getSingleCenterSignUpStepByRoute($this->request->attributes->get('_route'));

                // TODO: check and redirect properly to next page if institution's sign up status is ahead of this step

                $response = $this->setupProfileSingleCenterAction();
                break;
            case InstitutionTypes::MULTIPLE_CENTER:
            //case InstitutionTypes::MEDICAL_TOURISM_FACILITATOR:
            default:

                // get the current step by this route
                $this->currentSignUpStep = $this->signUpService->getMultipleCenterSignUpStepByRoute($this->request->attributes->get('_route'));

                // TODO: check and redirect properly to next page if institution's sign up status is ahead of this step

                $response = $this->setupProfileMultipleCenterAction($request);
                break;
        }

        return $response;
    }

    /**
     * Setting up the profile of the Single Center institution
     *
     * TODO:
     *     This has a crappy rule where institution name and description will internally be the name and description of the clinic.
     *
     * @author acgvelarde
     * @return
     */
    private function setupProfileSingleCenterAction()
    {
        $error_message = false;
        $success = false;
        $errorArr = array();
        $medicalProviderGroup = $this->getDoctrine()->getRepository('InstitutionBundle:MedicalProviderGroup')->getActiveMedicalGroups();
        $institutionMedicalCenter = $this->institutionService->getFirstMedicalCenter($this->institution);

        if (!$this->institutionService->isSingleCenter($this->institution)) {
            // this is not a single center institution, where will we redirect it? for now let us redirect it to dashboard
            // we should not be here in the first place
            return $this->redirect($this->generateUrl('institution_homepage'));
        }

        if (\is_null($institutionMedicalCenter)) {
            $institutionMedicalCenter = new InstitutionMedicalCenter();
        }

        $this->get('services.contact_detail')->initializeContactDetails($this->institution, array(ContactDetailTypes::PHONE));

        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false));

        if ($this->request->isMethod('POST')) {

            $formRequestData = $this->request->get($form->getName());

            if (isset($formRequestData['medicalProviderGroups']) ) {
                // we always expect 1 medical provider group
                // if it is empty remove it from the array
                if (isset($formRequestData['medicalProviderGroups'][0]) && '' == trim($formRequestData['medicalProviderGroups'][0]) ) {
                    unset($formRequestData['medicalProviderGroups'][0]);
                }
            }

            $form->bind($formRequestData);
            if ($form->isValid()) {
                $this->get('services.contact_detail')->removeInvalidContactDetails($this->institution);
                // set the sign up status of this single center institution
                $this->_updateInstitutionSignUpStepStatus($this->currentSignUpStep);
                $form->getData()->setSignupStepStatus($this->currentSignUpStep->getStepNumber());

                // Upload Logo
                if(($fileBag = $this->request->files->get('institution_profile_form')) && $fileBag['logo']) {
                    $this->get('services.institution.media')->uploadLogo($fileBag['logo'], $form->getData(), false);
                }
                // save institution and create an institution medical center
                $this->signUpService->completeProfileOfInstitutionWithSingleCenter($form->getData(), $institutionMedicalCenter);

                // save services and awards
                //$this->get('services.institution_property')->addPropertiesForInstitution($this->institution, $form['services']->getData(), $form['awards']->getData());

                // get the next step redirect url
                $redirectUrl = $this->generateUrl($this->signUpService->getSingleCenterSignUpNextStep($this->currentSignUpStep)->getRoute(), array('imcId' => $this->institutionService->getFirstMedicalCenter($this->institution)->getId()));

                //$request->getSession()->setFlash('callout', "");
                //$request->getSession()->setFlash('success', "<b>Congratulations!</b> You have setup your Clinic profile."); //set flash message

                // TODO: Update this when we have formulated a strategy for our event system
                // We can't use InstitutionBundleEvents; we don't know the consequences of the event firing up other listeners.
                $this->get('event_dispatcher')->dispatch(MailerBundleEvents::NOTIFICATIONS_HOSPITAL_CREATED, new GenericEvent($this->institution));

                return $this->redirect($redirectUrl);
            }
            $error = true;
            $form_errors = $this->get('validator')->validate($form);

            if($form_errors){
                $error_message = 'We need you to correct some of your input. Please check the fields in red.';
            }
        }

        $medicalProviderGroupArr = array();

        foreach ($medicalProviderGroup as $e) {
            $medicalProviderGroupArr[] = array('value' => $e->getName(), 'id' => $e->getId());
        }
        return $this->render('InstitutionBundle:SignUp:setupProfile.singleCenter.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalCenter' => $institutionMedicalCenter,
            'isSingleCenter' => true,
            'error_message' => $error_message,
            'medicalProvidersJSON' => \json_encode($medicalProviderGroupArr)
        ));
    }

    /**
     * Setting up profile of multiple center institution
     *
     * @param Request $request
     */
    private function setupProfileMultipleCenterAction(Request $request)
    {
        $error_message = '';
        $success = false;
        $medicalProviderGroup = $this->getDoctrine()->getRepository('InstitutionBundle:MedicalProviderGroup')->getActiveMedicalGroups();

        $this->get('services.contact_detail')->initializeContactDetails($this->institution, array(ContactDetailTypes::PHONE));

        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false));

        if ($request->isMethod('POST')) {
            $formRequestData = $request->get($form->getName());

            if (isset($formRequestData['medicalProviderGroups']) ) {
                // we always expect 1 medical provider group
                // if it is empty remove it from the array
                if (isset($formRequestData['medicalProviderGroups'][0]) && '' == trim($formRequestData['medicalProviderGroups'][0]) ) {
                    unset($formRequestData['medicalProviderGroups'][0]);
                }
            }

            $form->bind($formRequestData);
            if ($form->isValid()) {
                $this->get('services.contact_detail')->removeInvalidContactDetails($this->institution);

                $em = $this->getDoctrine()->getManager();
                $em->persist($this->institution);

                // set sign up status to current step number
                $this->_updateInstitutionSignUpStepStatus($this->currentSignUpStep);
                $form->getData()->setSignupStepStatus($this->currentSignUpStep->getStepNumber());

                $fileBag = $request->files->get('institution_profile_form');

                if($fileBag['logo']) {
                    $this->get('services.institution.media')->uploadLogo($fileBag['logo'], $form->getData(), false);
                }

                if($fileBag['featuredMedia']) {
                    $this->get('services.institution.media')->uploadFeaturedImage($fileBag['featuredMedia'], $form->getData(), false);
                }

                $this->signUpService->completeProfileOfInstitutionWithMultipleCenter($form->getData());

                $this->get('services.institution_property')->addPropertiesForInstitution($this->institution, $form['services']->getData(), $form['awards']->getData());

                //$calloutMessage = $this->get('services.institution.callouts')->get('signup_multiple_center_success');
                //$this->getRequest()->getSession()->getFlashBag()->add('callout_message', $calloutMessage);
                $request->getSession()->setFlash('callout', "");
                $request->getSession()->setFlash('success', "<b>Congratulations!</b> You have setup your Hospital's profile."); //set flash message
                $redirectUrl = $this->generateUrl($this->signUpService->getMultipleCenterSignUpNextStep($this->currentSignUpStep)->getRoute());

                // TODO: Update this when we have formulated a strategy for our event system
                // We can't use InstitutionBundleEvents; we don't know the consequences of the event firing up other listeners.
                $this->get('event_dispatcher')->dispatch(MailerBundleEvents::NOTIFICATIONS_HOSPITAL_CREATED, new GenericEvent($this->institution));

                return $this->redirect($redirectUrl);
            }
            $form_errors = $this->get('validator')->validate($form);

            if($form_errors){
                   $error_message = 'We need you to correct some of your input. Please check the fields in red.';
            }
        }

        $medicalProviderGroupArr = array();

        foreach ($medicalProviderGroup as $e) {
            $medicalProviderGroupArr[] = array('value' => $e->getName(), 'id' => $e->getId());
        }
        return $this->render('InstitutionBundle:SignUp:setupProfile.multipleCenter.html.twig', array(
            'form' => $form->createView(),
            'institution' => $this->institution,
            'error_message' => $error_message,
            'medicalProvidersJSON' => \json_encode($medicalProviderGroupArr)
        ));
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setupInstitutionMedicalCenterAction(Request $request)
    {
        $error_message = false;
        if ($this->institutionService->isSingleCenter($this->institution)){
            // this is not part of the sign up flow of  single center institution
            throw $this->createNotFoundException();
        }
        $this->currentSignUpStep = $this->signUpService->getMultipleCenterSignUpStepByRoute($request->attributes->get('_route'));

        // TODO: check current sign up status

        // We don't assume that there is a medical center instance here already since this is also where we redirect from multiple center institution sign up
        if (!$this->institutionMedicalCenter instanceof InstitutionMedicalCenter) {
            $this->institutionMedicalCenter = new InstitutionMedicalCenter();
            $this->institutionMedicalCenter->setInstitution($this->institution);
        }

        $this->get('services.contact_detail')->initializeContactDetails($this->institutionMedicalCenter, array(ContactDetailTypes::PHONE));

        $form = $this->createForm(new InstitutionMedicalCenterFormType($this->institution), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => false));

        if ($this->request->isMethod('POST')) {

            $formRequestData = $request->get($form->getName());

            if((bool)$request->get('isSameAddress')) {
                $formRequestData['address'] = json_decode($this->institution->getAddress1(), true);
                $this->institutionMedicalCenter->setAddressHint($this->institution->getAddressHint());
                $this->institutionMedicalCenter->setCoordinates($this->institution->getCoordinates());
            }
            $form->bind($formRequestData);
            if ($form->isValid()) {
                $institutionMedicalCenterService = $this->get('services.institution_medical_center');
                $institutionMedicalCenterService->clearBusinessHours($this->institutionMedicalCenter);

                $this->institutionMedicalCenter = $form->getData();
                $this->get('services.contact_detail')->removeInvalidContactDetails($this->institutionMedicalCenter);
                foreach ($this->institutionMedicalCenter->getBusinessHours() as $_hour ) {
                    $_hour->setInstitutionMedicalCenter($this->institutionMedicalCenter );
                }

                $institutionMedicalCenterService->saveAsDraft($this->institutionMedicalCenter);

                // update sign up step status of institution
                $this->_updateInstitutionSignUpStepStatus($this->currentSignUpStep, true);

                // redirect to next step
                $nextStepRoute = $this->signUpService->getMultipleCenterSignUpNextStep($this->currentSignUpStep)->getRoute();
                //$request->getSession()->setFlash('callout', "");
                //$request->getSession()->setFlash('success', "<b>Congratulations! </b> You have setup your clinic profile."); //set flash message

                // TODO: Update this when we have formulated a strategy for our events system
                // We can't use InstitutionBundleEvents; we don't know the consequences of the event firing up other listeners.
                $this->get('event_dispatcher')->dispatch(
                    MailerBundleEvents::NOTIFICATIONS_CLINIC_CREATED,
                    new GenericEvent($this->institutionMedicalCenter, array('userEmail' => $request->getSession()->get('userEmail'))));

                return $this->redirect($this->generateUrl($nextStepRoute, array('imcId' => $this->institutionMedicalCenter->getId())));
            }
            $form_errors = $this->get('validator')->validate($form);

            if($form_errors){
                   $error_message = 'We need you to correct some of your input. Please check the fields in red.';
            }
        }
        return $this->render('InstitutionBundle:SignUp:setupInstitutionMedicalCenter.html.twig', array(
            'form' => $form->createView(),
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'error_message' => $error_message,
        ));
    }

    public function setupSpecializationsAction(Request $request)
    {
        $error = '';
        $functionName = $this->isSingleCenter ? 'getSingleCenterSignUpStepByRoute' : 'getMultipleCenterSignUpStepByRoute';
        $this->currentSignUpStep = $this->signUpService->{$functionName}($request->attributes->get('_route'));

        $specializations = $this->get('services.institution_specialization')->getNotSelectedSpecializations($this->institution);

        if ($request->isMethod('POST')) {
            //array of specialization ids each containing an array of treatment ids
            if ($treatments = $request->get('treatments')) {
                $this->get('services.institution_medical_center')->addMedicalCenterSpecializationsWithTreatments($this->institutionMedicalCenter, $treatments);
                $this->_updateInstitutionSignUpStepStatus($this->currentSignUpStep, true);

                // next step url

                $redirectUrl = $this->signUpService->{($isSingleCenter?'getSingleCenterSignUpNextStep':'getMultipleCenterSignUpNextStep')}($this->currentSignUpStep)->getRoute();
                return $this->redirect($redirectUrl);
            }
            $error = 'Please select at least one specialization.';
        }

        return $this->render('InstitutionBundle:SignUp:setupSpecializations.html.twig', array(
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'specializations' => $specializations,
            'error' => $error,
        ));
    }

    public function setupDoctorsAction(Request $request)
    {

        //TODO: check institution signupStepStatus
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

            return new Response(json_encode($data), 200, array('Content-Type'=>'application/json'));
        }

        $params = array(
            'doctorForm' => $form->createView(),
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'thumbnailSize' => ImageSizes::DOCTOR_LOGO,
            'doctors' => $this->get('services.doctor')->doctorsObjectToArray($this->institutionMedicalCenter->getDoctors())
        );

        $editDoctor = new Doctor();
        if($this->institutionMedicalCenter->getDoctors()->count()) {
            $editDoctor = $this->institutionMedicalCenter->getDoctors()->first();
        }

        $this->get('services.contact_detail')->initializeContactDetails($editDoctor, array(ContactDetailTypes::PHONE));

        $editForm = $this->createForm(new InstitutionMedicalCenterDoctorFormType('editInstitutionMedicalCenterDoctorForm'), $editDoctor);
        $params['editForm'] = $editForm->createView();


        return $this->render('InstitutionBundle:SignUp:setupDoctors.html.twig', $params);
    }

    public function editDoctorAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId'));

        $form = $this->createForm(new InstitutionMedicalCenterDoctorFormType(), $doctor);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $doctor = $form->getData();

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($doctor);
                $em->flush($doctor);

                $data = array(
                    'status' => true,
                    'message' => 'Doctor has been added to your clinic!',
                    'doctor' => $this->get('services.doctor')->toArrayDoctor($doctor)
                );
            } else {
                $data = array('status' => false, 'message' => $form->getErrorsAsString());
            }

            return new Response(json_encode($data), 200, array('Content-Type'=>'application/json'));
        }

        return new Response(\json_encode($result),200, array('content-type' => 'application/json'));
    }

    /**
     * Note: This might be needed by other parts of the system. If so move this to
     * an appropriate and more generic controller.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxLoadSpecializationComponentsAction(Request $request)
    {
        //TODO: this will pull in additional component data not needed by our view layer. create another method on service class.
        $specializationComponents = $this->get('services.treatment_bundle')->getTreatmentsBySpecializationIdGroupedBySubSpecialization($request->get('specializationId'));
        $form = $this->createForm(new InstitutionSpecializationFormType(), new InstitutionSpecialization());

        $html = $this->renderView('InstitutionBundle:Widgets/Profile:specializations.listForm.html.twig', array(
            'form' => $form->createView(),
            'formName' => InstitutionSpecializationFormType::NAME,
            'specializationComponents' => $specializationComponents,
            'specializationId' => $request->get('specializationId'),
            'selectedTreatments' => ''
        ));

        return new Response($html, 200);
        //return new Response($html, 200, array('Content-Type'=>'application/json'));
    }

    public function finishAction(Request $request)
    {
        if($this->institution->getInstitutionMedicalCenters()->count() && $this->institution->getInstitutionMedicalCenters()->first()->getDoctors()->count()) {
            $this->institution->setSignupStepStatus(SignUpService::COMPLETED_SIGNUP_FLOW_STATUS);
            $this->get('services.institution.factory')->save($this->institution);
            $this->getRequest()->getSession()->set('institutionSignupStepStatus', $this->institution->getSignupStepStatus());
        }

        return $this->redirect($this->generateUrl('institution_homepage'));
    }

    private function getProxyMedicalCenter()
    {
        //This will have identical values with related institution
        $center = $this->institutionMedicalCenter;

        if ($this->request->isMethod('GET')) {
            $center->setName('');
            $center->setDescription('');
            //and other fields which we may have to reset...
        }

        return $center;
    }
}
