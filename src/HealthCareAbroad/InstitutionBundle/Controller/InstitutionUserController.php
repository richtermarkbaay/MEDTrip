<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserInvitationType;

use Guzzle\Http\Message\Response;

use HealthCareAbroad\UserBundle\Event\UserEvents;

use HealthCareAbroad\UserBundle\Event\CreateInstitutionUserEvent;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use Chromedia\AccountBundle\Entity\Account;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;

use ChromediaUtilities\Helpers\SecurityHelper;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionUserController extends Controller
{
    public function loginAction()
    {
        $user = new InstitutionUser();
        $form = $this->createFormBuilder($user)
            ->add('email', 'email', array('property_path'=> false))
            ->add('password', 'password', array('property_path'=> false))
            ->getForm();
        
        if ($this->getRequest()->isMethod('POST')) {
            
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                if ($this->get('services.institution_user')->login($form->get('email')->getData(), $form->get('password')->getData())) {
                    // valid login
                    $this->get('session')->setFlash('flash.notice', 'Login successfully!');
                    
                    return $this->redirect($this->generateUrl('institution_homepage'));
	            }
                else {
                    // invalid login
                    $this->get('session')->setFlash('flash.notice', 'Email and Password is invalid.');
                    
                    return $this->redirect($this->generateUrl('institution_login'));
                }
            }
        }
        return $this->render('InstitutionBundle:InstitutionUser:login.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function logoutAction()
    {
        $this->get('security.context')->setToken(null);
        $this->getRequest()->getSession()->invalidate();
        return $this->redirect($this->generateUrl('main_homepage'));
    }
    
    public function changePasswordAction()
    {
    	
    	$institutionUser = new InstitutionUser();
    	$defaultData = array('email' => 'Type your message here');
    	
    	$form = $this->createFormBuilder($institutionUser)
    			->add('email','password',array('label' => 'Current Password'))
    			->add( 'password', 'repeated', array( 'type' => 'password', 'invalid_message' => 'Passwords do not match' ))
    			->getform();
    	
    	if ($this->getRequest()->isMethod('POST')) {
    		
    		$form->bindRequest($this->getRequest());
    		if ($form->isValid()) {
    			
    			//get user account in chromedia global accounts by accountID
    			$session = $this->getRequest()->getSession();
    			$accountId = $session->get('accountId');
    			$newPassword = $form->get('password')->getData();
    			
    			//check if given oldpassword is match
    			$user = $this->get('services.institution_user')->findByIdAndPassword($accountId, $form->get('email')->getData());
            	
    			////set new password to chromedia accounts
    			$password = SecurityHelper::hash_sha256($newPassword);
    			$institutionUser = $this->get('services.institution_user')->changePassword($institutionUser, $accountId, $password);
    			
    			if ($institutionUser) {
    				$this->get('session')->setFlash('flash.notice', "Password changed!");
    			}
    			else {
    				$this->get('session')->setFlash('flash.notice', "Failed to change password,old password isn't correct");
    			}
    		}
    			
    	}
    	return $this->render('InstitutionBundle:InstitutionUser:changePassword.html.twig', array(
            'form' => $form->createView()));
    }
    
    public function editAccountAction()
    {
    	
    	//get user account in chromedia global accounts by accountID
    	$session = $this->getRequest()->getSession();
    	$accountId = $session->get('accountId');
    	$institutionUser = $this->get('services.institution_user')->findById($accountId, true);
    	
    	//render form
    	$form = $this->createFormBuilder($institutionUser)
        	->add('firstName', 'text')
            ->add('middleName', 'text')
            ->add('lastName', 'text')
            ->getForm();
    	
     	 if ($this->getRequest()->isMethod('POST')) {
            
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
            	
            	//set new gathered data from form
            	$institutionUser->setFirstName($form->get('firstName')->getData());
            	$institutionUser->setMiddleName($form->get('middleName')->getData());
            	$institutionUser->setLastName($form->get('lastName')->getData());
            	$institutionUser->setStatus(SiteUser::STATUS_ACTIVE);
            	
            	$institutionUser = $this->get('services.institution_user')->update($institutionUser, $accountId);
            	if ($institutionUser) {
                    $this->get('session')->setFlash('flash.notice', "Successfully updated your account");
                }
                else {
                    $this->get('session')->setFlash('flash.notice', "Failed to update account!");
                }
            	
            }
            
     	 }
    	return $this->render('InstitutionBundle:InstitutionUser:editAccount.html.twig', array(
            'form' => $form->createView()));
    }
    public function inviteAction()
    {
        $institution = $this->get('services.institution')->getCurrentInstitution();
        
        $institutionUserInvitation = new InstitutionUserInvitation();
      
        $form = $this->createForm(new InstitutionUserInvitationType(), $institutionUserInvitation);
        
        if ($this->getRequest()->isMethod('POST')) {
            
            $form->bind($this->getRequest());
            
            var_dump($form->isValid()); exit;
            
            if ($form->isValid()){
                
                $sendingResult = $this->get('services.invitation')->sendInstitutionUserInvitation($institution, $institutionUserInvitation);
                
                if ($sendingResult) {
                    $this->get('session')->setFlash('flash.notice', "Invitation sent to {$institutionUserInvitation->getEmail()}");
                }
                else {
                    $this->get('session')->setFlash('flash.notice', "Failed to send invitation to {$institutionUserInvitation->getEmail()}");
                }
                
                return $this->redirect($this->generateUrl('institution_invite_user'));
            }
        }
        
        return $this->render('InstitutionBundle:Default:invite.html.twig', array(
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
        
        // create event regarding institution user creation
        $event = new CreateInstitutionUserEvent($institutionUser);
        $event->setTemporaryPassword($temporaryPassword);
        $event->setUsedInvitation($invitation);
        
        // dispatch the event
        $this->get('event_dispatcher')->dispatch(UserEvents::ON_CREATE_INSTITUTION_USER, $event);
        
        // login to institution
        $this->get('services.institution_user')->login($institutionUser->getEmail(), $temporaryPassword);

        // redirect to institution homepage        
        $this->get('session')->setFlash('flash.notice', 'You have successfuly accepted the invitation.');
        
        return $this->redirect($this->generateUrl('institution_homepage'));
    }
    
}