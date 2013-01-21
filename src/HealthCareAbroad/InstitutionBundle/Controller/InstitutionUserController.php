<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

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

    /*
    public function loginAction()
    {
        $user = new InstitutionUser();
        $form = $this->createForm(new UserLoginType());

        if ($this->getRequest()->isMethod('POST')) {

            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                if ($this->get('services.institution_user')->login($form->get('email')->getData(), $form->get('password')->getData())) {
                    // valid login
                    $this->get('session')->setFlash('success', 'Welcome '.$this->get('security.context')->getToken()->getUser().'!');
                    return $this->redirect($this->generateUrl('institution_homepage'));
                }
                else {
                    // invalid login
                    $this->get('session')->setFlash('notice', 'Either your email or password is wrong.');
                }
            }
            else {
                // invalid login
                $this->get('session')->setFlash('notice', 'Email and password are required.');
            }
        }
        return $this->render('InstitutionBundle:InstitutionUser:login.html.twig', array(
            'form' => $form->createView(),
        ));
    }*/

    public function loginAction()
    {
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


    public function logoutAction()
    {
        $this->get('security.context')->setToken(null);
        $this->getRequest()->getSession()->invalidate();
        return $this->redirect($this->generateUrl('institution_login'));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTIONS')")
     */
    public function editAccountAction()
    {
        $output = array();
        $accountId = $this->getRequest()->get('accountId', null);
        $session = $this->getRequest()->getSession();
        if (!$accountId){
            // no account id in parameter, editing currently logged in account
            $accountId = $session->get('accountId');
        }
        $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($session->get('institutionId'));
        $this->get('twig')->addGlobal('institution', $this->institution);
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $this->get('twig')->addGlobal('userName', $loggedUser instanceof SiteUser ? $loggedUser->getFullName() : $loggedUser->getUsername());
        $institutionUser = $this->get('services.institution_user')->findById($accountId, true); //get user account in chromedia global accounts by accountID

        if (!$institutionUser) {
            throw $this->createNotFoundException('Cannot update invalid account.');
        }
        
        $formChangePassword = $this->createForm(new InstitutionUserChangePasswordType(), $institutionUser);
        $form = $this->createForm(new UserAccountDetailType(), $institutionUser);

          if($this->getRequest()->isMethod('POST')){
                if ( array_key_exists("institutionUserChangePasswordType",$_POST)) {
                   
                    $formChangePassword->bindRequest($this->getRequest());
                    if ($formChangePassword->isValid()) {
                        $institutionUser->setPassword(SecurityHelper::hash_sha256($formChangePassword->get('new_password')->getData()));
                        try{
                        // dispatch event
                            $this->get('services.institution_user')->update($institutionUser);
                            $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_CHANGE_PASSWORD_INSTITUTION_USER, $this->get('events.factory')->create(InstitutionBundleEvents::ON_CHANGE_PASSWORD_INSTITUTION_USER, $institutionUser));
        
                            $output['alert'] ="Success! Updated Password";
                            $output['html'] = $this->renderView('InstitutionBundle:InstitutionUser:form.changePassword.html.twig', array(
                                            'formChangePassword' => $formChangePassword->createView(),
                                            'institutionUser' => $institutionUser
                            ));
                        }catch (\Exception $e) {
                                $response = new Response($e->getMessage(), 500);
                            }
                    }else{
                        $output['alert'] ="Failed to update password";
                    }
                }
                else {
                    $form->bindRequest($this->getRequest());
                    if ($form->isValid()) {
                        try{
                            $institutionUser = $this->get('services.institution_user')->update($institutionUser);
                            // create event on editAccount and dispatch
                            $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION_USER, $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION_USER, $institutionUser));
            
                            $output['alert'] ="Success! Updated Account";
                            $output['html'] = $this->renderView('InstitutionBundle:InstitutionUser:editProfileForm.html.twig', array(
                                            'form' => $form->createView(),
                                            'institutionUser' => $institutionUser
                            ));
                            
                        }catch (\Exception $e) {
                            $response = new Response($e->getMessage(), 500);
                        }
                    }
                }
                $response = new Response(\json_encode($output),200, array('content-type' => 'application/json'));
                
                return $response;
            }
        
        return $this->render('InstitutionBundle:InstitutionUser:editAccount.html.twig', array(
                        'form' => $form->createView(),
                        'formChangePassword' => $formChangePassword->createView(),
                        'institutionUser' => $institutionUser ));
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
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTIONS')")
     */
    public function viewAllAction()
    {
        $institutionService = $this->get('services.institution');

        $users = $institutionService->getAllStaffOfInstitution($this->institution);

        return $this->render('InstitutionBundle:InstitutionUser:viewAll.html.twig', array('users' => $users));
    }


}