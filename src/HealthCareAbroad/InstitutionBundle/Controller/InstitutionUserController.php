<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserSignUpFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserResetPasswordType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserPasswordToken;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionUserEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\UserBundle\Form\UserAccountDetailType;
use HealthCareAbroad\UserBundle\Form\UserLoginType;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserChangePasswordType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserInvitationType;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Chromedia\AccountBundle\Entity\Account;
use ChromediaUtilities\Helpers\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use Symfony\Component\Security\Core\SecurityContext;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class InstitutionUserController extends Controller
{
    protected $institution;

    /**
     * TODO: Move to authentication controller
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction()
    {
        // checking for security context here does not work since this is not firewalled
        // TODO: find a better approach
        if ($this->get('session')->get('accountId', null)) {
            // redirect to dashboard if there is an active session
            // redirecting to dashboard may cause infinite redirect, since session may have been saved
            //return $this->redirect($this->generateUrl('institution_homepage'));
        }
        
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render('InstitutionBundle:InstitutionUser:login.html.twig', array(
                        // last username entered by the user
                        'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                        'error'         => $error
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTIONS')")
     */
    public function editAccountAction(Request $request)
    {
        $error_message = false;
        $accountId = $request->get('accountId', null);
        $session = $request->getSession();
        if (!$accountId ){
            // no account id in parameter, editing currently logged in account
            $accountId = $session->get('accountId');
        }
        $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($session->get('institutionId'));
        $this->get('twig')->addGlobal('institution', $this->institution);
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $this->get('twig')->addGlobal('userName', $loggedUser instanceof SiteUser ? $loggedUser->getFullName() : $loggedUser->getUsername());
        $institutionUser = $this->get('services.institution_user')->findById($accountId, true); //get user account in chromedia global accounts by accountID
        $this->get('services.contact_detail')->initializeContactDetails($institutionUser, array(ContactDetailTypes::PHONE ,ContactDetailTypes::MOBILE ));
        
        $form = $this->createForm(new InstitutionUserSignUpFormType(), $institutionUser,  array('include_terms_agreement' => false, 'institution_types' => false));
        $em = $this->getDoctrine()->getManager();
        
          if($request->isMethod('POST')){
            $form->bind($request);
            if ($form->isValid()) {
                    $institutionUser = $form->getData();
                    $this->get('services.contact_detail')->removeInvalidContactDetails($institutionUser);
                    $this->get('services.institution_user')->update($institutionUser);
                    // create event on editAccount and dispatch
                    $this->get('services.institution_user')->setSessionVariables($institutionUser);
                    $this->get('session')->setFlash('success', 'You have successfuly edit your account.');
            }else{
                $form_errors = $this->get('validator')->validate($form);
                if($form_errors){
                    $error_message = 'We need you to correct some of your input. Please check the fields in red.';
                }
            }
        }
        
        return $this->render('InstitutionBundle:InstitutionUser:editAccount.html.twig', array(
                'form' => $form->createView(),
                'institutionUser' => $institutionUser,
                'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution),
                'error_message' => $error_message
        ));
    }
    
    public function inviteAction()
    {
        $institutionUserInvitation = new InstitutionUserInvitation();
        $form = $this->createForm(new InstitutionUserInvitationType(), $institutionUserInvitation);

        if ($this->getRequest()->isMethod('POST')) {

            $form->bind($this->getRequest());
            if ($form->isValid()){
                $sendingResult = $this->get('services.invitation')->sendInstitutionUserInvitation($this->institution, $institutionUserInvitation);
                // dispatch event
                $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_USER_INVITATION, $this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION_USER_INVITATION, $institutionUserInvitation));
                $this->get('session')->setFlash('success', "Invitation sent to {$institutionUserInvitation->getEmail()}");

                return $this->redirect($this->generateUrl('institution_view_all_staff'));
            }
        }

        return $this->render('InstitutionBundle:InstitutionUser:invite.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function acceptInvitationAction()
    {
        // validate token
        $token = $this->getRequest()->get('token', null);
        $invitation = $this->get('services.token')->getActiveInstitutionUserInvitationByToken($token);

        if (!$invitation) {
            throw $this->createNotFoundException('Invalid token');
        }

        //TODO: get the matching institution user type
        $institutionUserType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find(1);

        // create temporary 10 character password
        $temporaryPassword = \substr(SecurityHelper::hash_sha256(time()), 0, 10);

        // create a institution user
        $institutionUser = new InstitutionUser();
        $institutionUser->setInstitution($invitation->getInstitution());
        $institutionUser->setInstitutionUserType($institutionUserType);
        $institutionUser->setEmail($invitation->getEmail());
        $institutionUser->setPassword($temporaryPassword);
        $institutionUser->setFirstName($invitation->getFirstName());
        $institutionUser->setMiddleName($invitation->getMiddleName());
        $institutionUser->setLastName($invitation->getLastName());
        $institutionUser->setStatus(SiteUser::STATUS_ACTIVE);
        $this->get('services.institution_user')->create($institutionUser);

        // dispatch event regarding institution user creation
        $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_USER, $this->get('events.factory')
            ->create(InstitutionBundleEvents::ON_ADD_INSTITUTION_USER, $institutionUser, array(
                CreateInstitutionUserEvent::OPTION_TEMPORARY_PASSWORD => $temporaryPassword,
                CreateInstitutionUserEvent::OPTION_USED_INVITATION => $invitation,
        )));

        // login to institution
        //$this->get('services.institution_user')->login($institutionUser->getEmail(), $temporaryPassword);

        // redirect to institution homepage
        $this->get('session')->setFlash('success', 'You have successfuly accepted the invitation.');

        return $this->redirect($this->generateUrl('institution_homepage'));
    }
    
    public function resetPasswordAction(Request $request)
    {
        $institutionUserService = $this->get('services.institution_user');
            if ($request->isMethod('POST')) {
                $email = $request->get('email');            
                $accountId = $this->get('services.institution_user')->findByEmail($email);
                if($accountId){
                    //generate token
                    $daysOfExpiration = 7;
                    $token = $this->get('services.institution_user')->createInstitutionUserPasswordToken($daysOfExpiration, $accountId);
                    
                    //send email here
                    
                    $this->get('session')->setFlash('notice', 'Next Step: Please check your email and follow the instructions that we\'ve just sent you.');
                    return $this->redirect($this->generateUrl('institution_login'));
                }
                $this->get('session')->setFlash('error', 'The email address you entered does not exist. Please check and try again.');
            }
            
        return $this->render('InstitutionBundle:InstitutionUser:requestResetPassword.html.twig');
    }

    public function changePasswordAction(Request $request){
        
        $institutionUserService = $this->get('services.institution_user');
        if($token = $request->get('token')) {
            $institutionUserToken = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionUserPasswordToken')->findOneByToken($token);
            $accountId = $institutionUserToken->getAccountId();
            $institutionUser = $institutionUserService->findById($accountId, true);
            $form = $this->createForm(new InstitutionUserResetPasswordType(), $institutionUser);
        
            if ($request->isMethod('POST')) {
                $form->bind($request);
                if ($form->isValid()) {
                    $institutionUser->setPassword(SecurityHelper::hash_sha256($form->get('new_password')->getData()));
                    $institutionUser = $institutionUserService->deleteInstitutionUserPasswordToken($institutionUserToken, $institutionUser);
        
                    //auto login
                    $roles = $institutionUserService->getUserRolesForSecurityToken($institutionUser);
                    $securityToken = new UsernamePasswordToken($institutionUser,$institutionUser->getPassword() , 'institution_secured_area', $roles);
                    $this->get('session')->set('_security_institution_secured_area',  \serialize($securityToken));
                    $this->get('security.context')->setToken($securityToken);
                    $institutionUserService->setSessionVariables($institutionUser);
        
                    return $this->redirect($this->generateUrl('institution_homepage'));
                }
            }
        
            $params = array(
                            'token' => $token,
                            'form' => $form->createView()
            );
        }
        return $this->render('InstitutionBundle:InstitutionUser:resetPassword.html.twig',$params);
    }
}