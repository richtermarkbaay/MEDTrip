<?php
/*
 * @author Alnie Jacobe
 */

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionProfileFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSignupStepStatus;

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

    public function preExecute()
    {
        if ($imcId = $this->getRequest()->get('imcId', 0)) {
            $this->institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($imcId);
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
        if ($this->get('session')->get('institutionId')) {
            // redirect to dashboard if there is an active session
            return $this->redirect($this->generateUrl('institution_homepage'));
        }
        $factory = $this->get('services.institution.factory');
        $institution = $factory->createInstance();
        $form = $this->createForm(new InstitutionSignUpFormType(), $institution);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {

                $institution = $form->getData();

                // initialize required database fields
                $institution->setName(uniqid());
                  $institution->setAddress1('');
                $institution->setContactEmail('');
                $institution->setContactNumber('');
                $institution->setDescription('');
                $institution->setCoordinates('');
                $institution->setState('');
                $institution->setWebsites('');
                $institution->setStatus(InstitutionStatus::getBitValueForInactiveStatus());
                $institution->setZipCode('');
                $institution->setSignupStepStatus(InstitutionSignupStepStatus::STEP1);

                $factory->save($institution);

                // create Institution user
                $institutionUser = new InstitutionUser();
                $institutionUser->setEmail($form->get('email')->getData());
                $institutionUser->setFirstName($form->get('firstName')->getData());
                $institutionUser->setLastName($form->get('lastName')->getData());
                $institutionUser->setContactNumber($form->get('contactNumber')->getData());
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

        return $this->render('InstitutionBundle:Institution:signUp.html.twig', array(
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

        $this->confirmationMessage = '<b>Congratulations!</b> Your account has been successfully created.';
        $this->request = $this->getRequest();
        switch ($this->institution->getType())
        {
            case InstitutionTypes::SINGLE_CENTER:
                $response = $this->setupProfileSingleCenterAction();
                break;
            case InstitutionTypes::MULTIPLE_CENTER:
            case InstitutionTypes::MEDICAL_TOURISM_FACILITATOR:
            default:
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
    public function setupProfileSingleCenterAction()
    {
        $error = false;
        $success = false;
        $errorArr = array();

        $institutionService = $this->get('services.institution');
        $institutionMedicalCenter = $institutionService->getFirstMedicalCenter($this->institution);

        if((int)$this->institution->getSignupStepStatus() === 0 && $institutionMedicalCenter) {
            $routeName = InstitutionSignupStepStatus::getRouteNameByStatus($this->institution->getSignupStepStatus());
            return $this->redirect($this->generateUrl($routeName));
        }

        if (!$institutionService->isSingleCenter($this->institution)) {
            // this is not a single center institution, where will we redirect it? for now let us redirect it to dashboard
            return $this->redirect($this->generateUrl('institution_homepage'));
        }

        if (\is_null($institutionMedicalCenter)) {
            $institutionMedicalCenter = new InstitutionMedicalCenter();
        }

        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution , array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false));

        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);

            if ($form->isValid()) {

                // save institution and create an institution medical center
                $this->get('services.institution_signup')
                ->completeProfileOfInstitutionWithSingleCenter($form->getData(), $institutionMedicalCenter);

                $this->get('services.institution_property')
                ->addPropertiesForInstitution($this->institution, $form['services']->getData(), $form['awards']->getData());

                //TODO: update getRouteNameByStatus to reflect changes in the flow
                //$routeName = InstitutionSignupStepStatus::getRouteNameByStatus($this->institution->getSignupStepStatus());
                $routeName = 'institution_signup_setup_institutionMedicalCenter';

                // this should redirect to 2nd step
                return $this->redirect($this->generateUrl($routeName, array('imcId' => $institutionMedicalCenter->getId())));
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
            'confirmationMessage' => $this->confirmationMessage,
            'error' => $error,
            'error_list' => $errorArr
        ));
    }

    /**
     * Setting up profile of multiple center institution
     *
     * @param Request $request
     */
    public function setupProfileMultipleCenterAction()
    {
        $error = false;
        $success = false;
        $errorArr = array();

        if((int)$this->institution->getSignupStepStatus() === 0) {
            return $this->redirect($this->generateUrl('institution_account_profile'));
        }

        $form = $this->createForm(new InstitutionProfileFormType(), $this->institution, array(InstitutionProfileFormType::OPTION_BUBBLE_ALL_ERRORS => false));
        $institutionTypeLabels = InstitutionTypes::getLabelList();

        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);
            if ($form->isValid()) {

                $this->get('services.institution_signup')
                ->completeProfileOfInstitutionWithMultipleCenter($form->getData());

                $this->get('services.institution_property')
                ->addPropertiesForInstitution($this->institution, $form['services']->getData(), $form['awards']->getData());

                $calloutMessage = $this->get('services.institution.callouts')->get('signup_multiple_center_success');
                $this->getRequest()->getSession()->getFlashBag()->add('callout_message', $calloutMessage);

                //TODO: redirect to add specializations
                return $this->redirect($this->generateUrl('institution_homepage'));
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
            'confirmationMessage' => $this->confirmationMessage,
            'error' => $error,
            'error_list' => $errorArr,
        ));
    }

    /**
     * This step is invoked only after a single center institution signup.
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function setupInstitutionMedicalCenterAction()
    {
        //TODO: check institution signupStepStatus

        $proxyMedicalCenter = $this->getProxyMedicalCenter();

        $form = $this->createForm(new InstitutionMedicalCenterFormType(), $proxyMedicalCenter, array(InstitutionMedicalCenterFormType::OPTION_BUBBLE_ALL_ERRORS => true));

        if ($this->request->isMethod('POST')) {
            $form->bind($this->request);

            if ($form->isValid()) {
                $institutionMedicalCenter = $form->getData();

                $this->get('services.institution_medical_center')->saveAsDraft($institutionMedicalCenter);

                //save other data here

                $this->redirect($this->generateUrl('institution_signup_setup_specializations', array('imcId' => $institutionMedicalCenter->getId())));
            } else {
                var_dump($form->getErrorsAsString());
            }
        }

        return $this->render('InstitutionBundle:SignUp:setupInstitutionMedicalCenter.html.twig', array(
                        'form' => $form->createView(),
                        'institution' => $this->institution,
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'isSingleCenter' => true,
                        'confirmationMessage' => "<b>Congratulations!</b> You have setup your Hospital's profile."
        ));
    }

    public function setupSpecializationsAction(Request $request)
    {
        //TODO: check institution signupStepStatus

        $specializations = $this->get('services.treatment_bundle')->getAllActiveSpecializations();

        if ($request->isMethod('POST')) {
            //TODO: validation

            //array of specialization ids each containing an array of treatment ids
            $treatments = $request->get('treatments');

            if ($treatments) {
                $this->get('services.institution_medical_center')->addMedicalCenterSpecializationsWithTreatments($this->institutionMedicalCenter, $treatments);
            }

            $this->redirect($this->generateUrl('institution_signup_setup_doctors', array('imcId' => $this->institutionMedicalCenter->getId())));
        }

        return $this->render('InstitutionBundle:SignUp:setupSpecializations.html.twig', array(
                        'institution' => $this->institution,
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'specializations' => $specializations,
                        'confirmationMessage' => "<b>Congratulations!</b> Your account has been successfully created."
        ));
    }

    public function setupDoctorsAction(Request $request)
    {
        //TODO: check institution signupStepStatus

        return $this->render('InstitutionBundle:SignUp:setupDoctors.html.twig', array(
                        'institution' => $this->institution,
                        'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'confirmationMessage' => "<b>Congratulations!</b> Your account has been successfully created."
        ));
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
                        'specializationId' => $request->get('specializationId')
        ));

        return new Response($html, 200);
        //return new Response($html, 200, array('Content-Type'=>'application/json'));
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
