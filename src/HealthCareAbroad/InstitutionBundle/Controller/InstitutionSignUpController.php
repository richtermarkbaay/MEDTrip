<?php
/*
 * @author Alnie Jacobe
 */

namespace HealthCareAbroad\InstitutionBundle\Controller;

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
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($imcId);
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
        $error = false;
        $success = false;
        $errorArr = array();
        // checking for security context here does not work since this is not firewalled
        // TODO: find a better approach
//         if ($this->get('session')->get('institutionId')) {
            // redirect to dashboard if there is an active session
            //return $this->redirect($this->generateUrl('institution_homepage'));
//         }
        $factory = $this->get('services.institution.factory');
        $institution = $factory->createInstance();
        $institutionUser = new InstitutionUser();
        $phoneNumber = new ContactDetail();
        $phoneNumber->setType(ContactDetailTypes::PHONE);
        $institutionUser->addContactDetail($phoneNumber);
        
        $mobileNumber = new ContactDetail();
        $mobileNumber->setType(ContactDetailTypes::MOBILE);
        $institutionUser->addContactDetail($mobileNumber);
        $form = $this->createForm(new InstitutionUserSignUpFormType(), $institutionUser);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                
                $institutionUser = $form->getData();
                // initialize required database fields
                $institution->setName(uniqid());
                $institution->setAddress1('');
                $institution->setContactEmail('');
                $institution->setContactNumber('');
                $institution->setDescription('');
                $institution->setCoordinates('');
                $institution->setType($form->get('type')->getData());
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

                return $this->redirect($this->generateUrl('institution_signup_setup_profile'));
            }
            $error = true;
            $form_errors = $this->get('validator')->validate($form);
            if($form_errors){
                foreach ($form_errors as $_err) {
                    $errorArr[] = $_err->getMessage();
                }
            }
        }

        return $this->render('InstitutionBundle:SignUp:signUp.html.twig', array(
            'form' => $form->createView(),
            'institutionTypes' => InstitutionTypes::getFormChoices(),
            'error' => $error,
            'error_list' => $errorArr,
        ));
    }

    /**
     * Landing page after signing up as an Institution. Logic will differ depending on the type of institution
     *
     * @param Request $request
     */
    public function setupProfileAction()
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
            case InstitutionTypes::MEDICAL_TOURISM_FACILITATOR:
            default:
                // get the current step by this route
                $this->currentSignUpStep = $this->signUpService->getMultipleCenterSignUpStepByRoute($this->request->attributes->get('_route'));

                // TODO: check and redirect properly to next page if institution's sign up status is ahead of this step

                $response = $this->setupProfileMultipleCenterAction();
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
        $error = false;
        $success = false;
        $errorArr = array();

        $institutionMedicalCenter = $this->institutionService->getFirstMedicalCenter($this->institution);

        if (!$this->institutionService->isSingleCenter($this->institution)) {
            // this is not a single center institution, where will we redirect it? for now let us redirect it to dashboard
            // we should not be here in the first place
            return $this->redirect($this->generateUrl('institution_homepage'));
        }

        if (\is_null($institutionMedicalCenter)) {
            $institutionMedicalCenter = new InstitutionMedicalCenter();
        }
        $contactDetails = $this->institutionService->getContactDetailsByInstitution($this->institution);
        if(!$contactDetails) {
            $phoneNumber = new ContactDetail();
            $phoneNumber->setType(ContactDetailTypes::PHONE);
            $this->institution->addContactDetail($phoneNumber);
        }
        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution , array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false));

        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);

            if ($form->isValid()) {

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

                return $this->redirect($redirectUrl);
            }
            $error = true;
            $form_errors = $this->get('validator')->validate($form);

            if($form_errors){

                foreach ($form_errors as $_err) {

                    $errorArr[] = $_err->getMessage();
                }
            }
        }

        return $this->render('InstitutionBundle:SignUp:setupProfile.singleCenter.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalCenter' => $institutionMedicalCenter,
            'isSingleCenter' => true,
            'error' => $error,
            'error_list' => $errorArr
        ));
    }

    /**
     * Setting up profile of multiple center institution
     *
     * @param Request $request
     */
    private function setupProfileMultipleCenterAction()
    {
        $error = false;
        $success = false;
        $errorArr = array();
        
        $contactDetails = $this->institutionService->getContactDetailsByInstitution($this->institution);

        if(!$contactDetails) {
            $phoneNumber = new ContactDetail();
            $phoneNumber->setType(ContactDetailTypes::PHONE);
            $this->institution->addContactDetail($phoneNumber);
        }
        
        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false));
        $institutionTypeLabels = InstitutionTypes::getLabelList();

        if ($this->request->isMethod('POST')) {

            $form->bind($this->request);

            if ($form->isValid()) {                
                // set sign up status to current step number
                $this->_updateInstitutionSignUpStepStatus($this->currentSignUpStep);
                $form->getData()->setSignupStepStatus($this->currentSignUpStep->getStepNumber());

                $fileBag = $this->request->files->get('institution_profile_form');

                if($fileBag['logo']) {
                    $this->get('services.institution.media')->uploadLogo($fileBag['logo'], $form->getData(), false);
                }

                if($fileBag['featuredMedia']) {
                    $this->get('services.institution.media')->uploadFeaturedImage($fileBag['featuredMedia'], $form->getData(), false);
                }

                $this->signUpService->completeProfileOfInstitutionWithMultipleCenter($form->getData());

                $this->get('services.institution_property')->addPropertiesForInstitution($this->institution, $form['services']->getData(), $form['awards']->getData());

                $calloutMessage = $this->get('services.institution.callouts')->get('signup_multiple_center_success');
                $this->getRequest()->getSession()->getFlashBag()->add('callout_message', $calloutMessage);

                $redirectUrl = $this->generateUrl($this->signUpService->getMultipleCenterSignUpNextStep($this->currentSignUpStep)->getRoute());

                return $this->redirect($redirectUrl);
            }
            $error = true;
            $form_errors = $this->get('validator')->validate($form);


            if($form_errors){
                foreach ($form_errors as $_err) {
                    $errorArr[] = $_err->getMessage();
                }
            }
        }

        return $this->render('InstitutionBundle:SignUp:setupProfile.multipleCenter.html.twig', array(
            'form' => $form->createView(),
            'institution' => $this->institution,
            'institutionTypeLabel' => $institutionTypeLabels[$this->institution->getType()],
            'error' => $error,
            'error_list' => $errorArr,
        ));
    }

    /**
     * 
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setupInstitutionMedicalCenterAction(Request $request)
    {
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
        else {
            
        }

        $contactDetails = $this->get('services.institution_medical_center')->getContactDetailsByInstitutionMedicalCenter($this->institutionMedicalCenter);

        if(!$contactDetails) {
            $phoneNumber = new ContactDetail();
            $phoneNumber->setType(ContactDetailTypes::PHONE);
            $this->institutionMedicalCenter->addContactDetail($phoneNumber);
        }

        $form = $this->createForm(new InstitutionMedicalCenterFormType(), $this->institutionMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => true));

        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);
            if ($form->isValid()) {
                $institutionMedicalCenterService = $this->get('services.institution_medical_center');
                $institutionMedicalCenterService->clearBusinessHours($this->institutionMedicalCenter);
                if((bool)$request->get('isSameAddress')) {

                    $this->institutionMedicalCenter->setAddress($this->institution->getAddress1());
                    $this->institutionMedicalCenter->setAddressHint($this->institution->getAddressHint());
                    $this->institutionMedicalCenter->setCoordinates($this->institution->getCoordinates());
                }
                
                $this->institutionMedicalCenter = $form->getData();
                
                foreach ($this->institutionMedicalCenter->getBusinessHours() as $_hour ) {
                    $_hour->setInstitutionMedicalCenter($this->institutionMedicalCenter );
                }

                $institutionMedicalCenterService->saveAsDraft($this->institutionMedicalCenter);

                // update sign up step status of institution
                $this->_updateInstitutionSignUpStepStatus($this->currentSignUpStep, true);

                // redirect to next step
                $nextStepRoute = $this->signUpService->getMultipleCenterSignUpNextStep($this->currentSignUpStep)->getRoute();
                
                return $this->redirect($this->generateUrl($nextStepRoute, array('imcId' => $this->institutionMedicalCenter->getId())));
            }
        }

        return $this->render('InstitutionBundle:SignUp:setupInstitutionMedicalCenter.html.twig', array(
            'form' => $form->createView(),
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter
        ));
    }

    public function setupSpecializationsAction(Request $request)
    {
        $isSingleCenter = $this->institutionService->isSingleCenter($this->institution);
        $this->currentSignUpStep = $this->signUpService->{($isSingleCenter?'getSingleCenterSignUpStepByRoute':'getMultipleCenterSignUpStepByRoute')}($request->attributes->get('_route'));
        
        //TODO: check institution signupStepStatus

        $specializations = $this->get('services.treatment_bundle')->getAllActiveSpecializations();

        if ($request->isMethod('POST')) {
            // TODO: validation

            //array of specialization ids each containing an array of treatment ids
            if ($treatments = $request->get('treatments')) {
                $this->get('services.institution_medical_center')->addMedicalCenterSpecializationsWithTreatments($this->institutionMedicalCenter, $treatments);
                $this->_updateInstitutionSignUpStepStatus($this->currentSignUpStep, true);

                // next step url
                $redirectUrl = $this->signUpService->{($isSingleCenter?'getSingleCenterSignUpNextStep':'getMultipleCenterSignUpNextStep')}($this->currentSignUpStep)->getRoute();

                return $this->redirect($redirectUrl);
            }
        }

        return $this->render('InstitutionBundle:SignUp:setupSpecializations.html.twig', array(
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'specializations' => $specializations,
        ));
    }

    public function setupDoctorsAction(Request $request)
    {

        //var_dump($request->headers->get('referer')); exit;
        
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
                    'doctor' => $this->get('services.doctor')->toArrayDoctor($doctor)
                );
            } else {
                $data = array('status' => false, 'message' => $form->getErrorsAsString());
            }

            return new Response(json_encode($data), 200, array('Content-Type'=>'application/json'));
        }

        $params = array(
            'form' => $form->createView(),
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'doctors' => $this->get('services.doctor')->doctorsObjectToArray($this->institutionMedicalCenter->getDoctors())
        );

        $editDoctor = new Doctor();
        if($this->institutionMedicalCenter->getDoctors()->count()) {
            $editDoctor = $this->institutionMedicalCenter->getDoctors()->first();            
        }

        if(!$editDoctor->getContactDetails()->count()) {
            $contactDetail = new ContactDetail();
            //$contactDetail->setType(ContactDetailTypes::MOBILE);
            $editDoctor->addContactDetail($contactDetail);
        }
        
        $editForm = $this->createForm(new InstitutionMedicalCenterDoctorFormType('editInstitutionMedicalCenterDoctorForm'), $editDoctor);
        $params['editForm'] = $editForm->createView();


        return $this->render('InstitutionBundle:SignUp:setupDoctors.html.twig', $params);
    }
    
    public function editDoctorAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId'));
        
        $form = $this->createForm(new InstitutionMedicalCenterDoctorFormType(), $doctor);
        
        var_dump($request->get('editInstitutionMedicalCenterDoctorForm')); exit;
        
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
        
        $html = $this->renderView('InstitutionBundle:Institution/Partials:specializationComponents.html.twig', array(
                        'specializationComponents' => $specializationComponents,
                        'specializationId' => $request->get('specializationId'),
                        'selectedTreatments' => ''
        ));

        return new Response($html, 200);
        //return new Response($html, 200, array('Content-Type'=>'application/json'));
    }

    public function ajaxEditDoctorAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('id', 0));
exit;
        if (!$doctor) {
            throw new \Exception('Invalid doctor');
        }

        $form = $this->createForm(new InstitutionSignUpDoctorFormType(), $doctor);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $doctor = $form->getData();

                $doctor = $this->signUpService->editDoctor($doctor, $this->getDoctrine());

                $rowDoctor = $this->renderView('InstitutionBundle:SignUp/Partials:row.doctor.html.twig', array('doctor' => $doctor));

                return new Response(json_encode(array('rowDoctor' => $rowDoctor)), 200, array('Content-Type'=>'application/json'));

            } else {
                var_dump($form->getErrorsAsString()); exit;
            }
        }

        $html = $this->renderView('InstitutionBundle:SignUp/Widgets:modalForm.doctors.html.twig', array(
                        'form' => $form->createView(),
                        'editMode' => true
        ));

        return new Response($html, 200, array('Content-Type'=>'application/json'));
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
