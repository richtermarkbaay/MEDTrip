<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserChangeEmailFormType;

use Symfony\Component\Form\Exception\NotValidException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\EventDispatcher\GenericEvent;

use HealthCareAbroad\MailerBundle\Event\MailerBundleEvents;

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
    
    public function editAccountPasswordAction(Request $request){
        $session = $request->getSession();
        $accountId = $session->get('accountId');
        $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($session->get('institutionId'));
        $this->get('twig')->addGlobal('institution', $this->institution);
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $this->get('twig')->addGlobal('userName', $loggedUser instanceof SiteUser ? $loggedUser->getFullName() : $loggedUser->getUsername());
        $institutionUserService = $this->get('services.institution_user');
        $institutionUser = $institutionUserService->findById($accountId, true); //get user account in chromedia global accounts by accountID
            
        if(!$institutionUser){
            throw new Access();
        }
        
        $form = $this->createForm(new InstitutionUserChangePasswordType(), $institutionUser);
        $em = $this->getDoctrine()->getManager();
        
        if($request->isMethod('POST')){
            $form->bind($request);
            if ($form->isValid()) {
                $institutionUser = $form->getData();
                
                // encrypt password here
                $institutionUser->setPassword($institutionUserService->encryptPassword($form->get('new_password')->getData()));
                
                $institutionUserService->update($institutionUser);
                $this->get('session')->setFlash('success', 'You have successfuly changed your password');
            }else{
                $this->get('session')->setFlash('error', 'We need you to correct some of your input. Please check the fields in red.');
            }
        }
        
        return $this->render('InstitutionBundle:InstitutionUser:changePassword.html.twig', array(
            'form' => $form->createView(),
            'institutionUser' => $institutionUser,
        ));
    }

    public function editAccountAction(Request $request)
    {
        $session = $request->getSession();
        
        $accountId = $session->get('accountId');
        $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($session->get('institutionId'));
        $this->get('twig')->addGlobal('institution', $this->institution);
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $this->get('twig')->addGlobal('userName', $loggedUser instanceof SiteUser ? $loggedUser->getFullName() : $loggedUser->getUsername());
        $institutionUserService = $this->get('services.institution_user');
        $institutionUser = $institutionUserService->findById($accountId, true); //get user account in chromedia global accounts by accountID
        
        if(!$institutionUser){
            
            throw new AccessDeniedHttpException();
        }
        
        $this->get('services.contact_detail')->initializeContactDetails($institutionUser, array(ContactDetailTypes::PHONE ,ContactDetailTypes::MOBILE ));
        $form = $this->createForm(new InstitutionUserSignUpFormType(), $institutionUser,  array('signup_fields' => false,'include_terms_agreement' => false, 'institution_types' => false));
        $em = $this->getDoctrine()->getManager();

          if($request->isMethod('POST')){
            $form->bind($request);
            if ($form->isValid()) {
                    $institutionUser = $form->getData();
                    $this->get('services.contact_detail')->removeInvalidContactDetails($institutionUser);
                    $institutionUserService->update($institutionUser);
                    $institutionUserService->setSessionVariables($institutionUser);
                    $this->get('session')->setFlash('success', 'You have successfuly edit your account.');
            }else{
                    $this->get('session')->setFlash('error', 'We need you to correct some of your input. Please check the fields in red.');
            }
        }

        return $this->render('InstitutionBundle:InstitutionUser:editAccount.html.twig', array(
                'form' => $form->createView(),
                'institutionUser' => $institutionUser,
                'isSingleCenter' => $this->get('services.institution')->isSingleCenter($this->institution),
        ));
    }
    
    public function editAccountEmailAction(Request $request){
        $session = $request->getSession();
        $accountId = $session->get('accountId');
        $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($session->get('institutionId'));
        $this->get('twig')->addGlobal('institution', $this->institution);
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $this->get('twig')->addGlobal('userName', $loggedUser instanceof SiteUser ? $loggedUser->getFullName() : $loggedUser->getUsername());
        $institutionUserService = $this->get('services.institution_user');
        $institutionUser = $institutionUserService->findById($accountId, true); //get user account in chromedia global accounts by accountID
    
        if(!$institutionUser){
            throw new AccessDeniedHttpException();
        }
    
        $form = $this->createForm(new InstitutionUserChangeEmailFormType(), $institutionUser);
        $em = $this->getDoctrine()->getManager();
    
        if($request->isMethod('POST')){
            $form->bind($request);
            if ($form->isValid()) {
                $institutionUser = $form->getData();

                $institutionUser->setEmail($form->get('new_email')->getData());
    
                $institutionUserService->update($institutionUser);
                $institutionUserService->setSessionVariables($institutionUser);
                $this->get('session')->setFlash('success', 'You have successfuly changed your email address');
            }else{
                $this->get('session')->setFlash('error', 'We need you to correct some of your input. Please check the fields in red.');
            }
        }
    
        return $this->render('InstitutionBundle:InstitutionUser:changeEmail.html.twig', array(
                        'form' => $form->createView(),
                        'institutionUser' => $institutionUser,
        ));
    }
    
    /**
     * NOTE: Not currently being used! DEPRECATED??
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function inviteAction(Request $request)
    {
        $institutionId = $request->getSession()->get('institutionId');
        $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);

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
            'institution' => $this->institution,
            'form' => $form->createView()
        ));
    }
    
    public function resetPasswordAction(Request $request)
    {
        $institutionUserService = $this->get('services.institution_user');
        if ($request->isMethod('POST')) {
            $email = $request->get('email');
            $accountId = $institutionUserService->findByEmail($email);
            if($accountId){
                //generate token
                $daysOfExpiration = 7;
                $token = $institutionUserService->createInstitutionUserPasswordToken($daysOfExpiration, $accountId);

                //listener will propagate any exceptions caught
                try {
                    $this->get('event_dispatcher')->dispatch(MailerBundleEvents::NOTIFICATIONS_PASSWORD_RESET, new GenericEvent(array(
                        'email' => $email, 'expiresIn' => $daysOfExpiration, 'token' => $token->getToken())));
                } catch (\Exception $e) {
                    //TODO:
                    // 1. finetune exception and messages
                    // 2. remove token
                    $this->get('session')->setFlash('notice', 'Connection could not be established with our email servers. Please try your request later.');

                    return $this->render('InstitutionBundle:InstitutionUser:requestResetPassword.html.twig');
                }

                $this->get('session')->setFlash('notice', 'Next Step: Please check your email and follow the instructions that we\'ve just sent you.');
                return $this->redirect($this->generateUrl('institution_login'));
            }
            $this->get('session')->setFlash('error', 'The email address you entered does not exist. Please check and try again.');
        }

        return $this->render('InstitutionBundle:InstitutionUser:requestResetPassword.html.twig');
    }

    /**
     * TODO: Better way of informing user of exceptions
     */
    public function changePasswordAction(Request $request)
    {
        $token = $request->get('token');
        $institutionUserService = $this->get('services.institution_user');
        if (!$institutionUserToken = $institutionUserService->findUnexpiredUserPasswordToken($token)) {
            throw new NotFoundHttpException('Invalid or expired token.');
        }
        if (!$institutionUser = $institutionUserService->findById($institutionUserToken->getAccountId(), true)) {
            throw new NotFoundHttpException('User not found.');
        }

        $form = $this->createForm(new InstitutionUserResetPasswordType(), $institutionUser);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $institutionUser->setPassword($institutionUserService->encryptPassword($form->get('new_password')->getData()));
                // FIXME: the service is using a confusing method name; a more appropriate name can
                // be updateInstitutionUser, for instance; institution user is also persisted in this
                // service method but this operation should be distinct from deleting the token or
                // the intent be made more explicit;
                $institutionUser = $institutionUserService->deleteInstitutionUserPasswordToken($institutionUserToken, $institutionUser);

                //auto login
                $roles = $institutionUserService->getUserRolesForSecurityToken($institutionUser);
                $securityToken = new UsernamePasswordToken($institutionUser,$institutionUser->getPassword() , 'institution_secured_area', $roles);
                $this->get('session')->set('_security_institution_secured_area',  \serialize($securityToken));
                $this->get('security.context')->setToken($securityToken);
                $institutionUserService->setSessionVariables($institutionUser);

                //send email here
                try {
                    $this->get('event_dispatcher')->dispatch(MailerBundleEvents::NOTIFICATIONS_PASSWORD_CONFIRM, new GenericEvent(array('email' => $institutionUser->getEmail())));
                } catch (\Exception $e) {
                    //TODO: inform user of failed email notif
                }

                return $this->redirect($this->generateUrl('institution_homepage'));
            }
        }

        return $this->render('InstitutionBundle:InstitutionUser:resetPassword.html.twig', array('token' => $token, 'form' => $form->createView()));
    }
}