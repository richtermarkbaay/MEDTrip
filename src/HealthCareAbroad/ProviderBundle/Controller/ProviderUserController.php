<?php

namespace HealthCareAbroad\ProviderBundle\Controller;

use HealthCareAbroad\ProviderBundle\Form\ProviderUserInvitationType;

use Guzzle\Http\Message\Response;

use HealthCareAbroad\UserBundle\Event\UserEvents;

use HealthCareAbroad\UserBundle\Event\CreateProviderUserEvent;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use Chromedia\AccountBundle\Entity\Account;

use HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation;

use ChromediaUtilities\Helpers\SecurityHelper;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProviderUserController extends Controller
{
    public function loginAction()
    {
        $user = new ProviderUser();
        $form = $this->createFormBuilder($user)
            ->add('email', 'email', array('property_path'=> false))
            ->add('password', 'password', array('property_path'=> false))
            ->getForm();
        
        if ($this->getRequest()->isMethod('POST')) {
            
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                if ($this->get('services.provider_user')->login($form->get('email')->getData(), $form->get('password')->getData())) {
                    // valid login
                    $this->get('session')->setFlash('flash.notice', 'Login successfully!');
                    
                    return $this->redirect($this->generateUrl('provider_homepage'));
	            }
                else {
                    // invalid login
                    $this->get('session')->setFlash('flash.notice', 'Email and Password is invalid.');
                    
                    return $this->redirect($this->generateUrl('provider_login'));
                }
            }
        }
        return $this->render('ProviderBundle:ProviderUser:login.html.twig', array(
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
    	
    	$providerUser = new ProviderUser();
    	$defaultData = array('email' => 'Type your message here');
    	
    	$form = $this->createFormBuilder($providerUser)
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
    			$user = $this->get('services.provider_user')->findByIdAndPassword($accountId, $form->get('email')->getData());
            	
    			////set new password to chromedia accounts
    			$password = SecurityHelper::hash_sha256($newPassword);
    			$providerUser = $this->get('services.provider_user')->changePassword($providerUser, $accountId, $password);
    			
    			if ($providerUser) {
    				$this->get('session')->setFlash('flash.notice', "Password changed!");
    			}
    			else {
    				$this->get('session')->setFlash('flash.notice', "Failed to change password,old password isn't correct");
    			}
    		}
    			
    	}
    	return $this->render('ProviderBundle:ProviderUser:changePassword.html.twig', array(
            'form' => $form->createView()));
    }
    
    public function editAccountAction()
    {
    	
    	//get user account in chromedia global accounts by accountID
    	$session = $this->getRequest()->getSession();
    	$accountId = $session->get('accountId');
    	$providerUser = $this->get('services.provider_user')->findById($accountId, true);
    	
    	//render form
    	$form = $this->createFormBuilder($providerUser)
        	->add('firstName', 'text')
            ->add('middleName', 'text')
            ->add('lastName', 'text')
            ->getForm();
    	
     	 if ($this->getRequest()->isMethod('POST')) {
            
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
            	
            	//set new gathered data from form
            	$providerUser->setFirstName($form->get('firstName')->getData());
            	$providerUser->setMiddleName($form->get('middleName')->getData());
            	$providerUser->setLastName($form->get('lastName')->getData());
            	$providerUser->setStatus(SiteUser::STATUS_ACTIVE);
            	
            	$providerUser = $this->get('services.provider_user')->update($providerUser, $accountId);
            	if ($providerUser) {
                    $this->get('session')->setFlash('flash.notice', "Successfully updated your account");
                }
                else {
                    $this->get('session')->setFlash('flash.notice', "Failed to update account!");
                }
            	
            }
            
     	 }
    	return $this->render('ProviderBundle:ProviderUser:editAccount.html.twig', array(
            'form' => $form->createView()));
    }
    public function inviteAction()
    {
        $provider = $this->get('services.provider')->getCurrentProvider();
        
        $providerUserInvitation = new ProviderUserInvitation();
//         $providerUserInvitation->setProvider($provider);
//         $providerUserInvitation->setDateCreated(new \DateTime('now'));
        
        $form = $this->createForm(new ProviderUserInvitationType(), $providerUserInvitation);
        
        if ($this->getRequest()->isMethod('POST')) {
            
            $form->bind($this->getRequest());
            
            var_dump($form->isValid()); exit;
            
            if ($form->isValid()){
                
                $sendingResult = $this->get('services.invitation')->sendProviderUserInvitation($provider, $providerUserInvitation);
                
                if ($sendingResult) {
                    $this->get('session')->setFlash('flash.notice', "Invitation sent to {$providerUserInvitation->getEmail()}");
                }
                else {
                    $this->get('session')->setFlash('flash.notice', "Failed to send invitation to {$providerUserInvitation->getEmail()}");
                }
                
                return $this->redirect($this->generateUrl('provider_invite_user'));
            }
        }
        
        return $this->render('ProviderBundle:Default:invite.html.twig', array(
                        'form' => $form->createView(),
        ));
    }
    
    public function acceptInvitationAction()
    {
        // validate token
        $token = $this->getRequest()->get('token', null);
        $invitation = $this->get('services.token')->getActiveProviderUserInvitatinByToken($token);
        
        if (!$invitation) {
            throw $this->createNotFoundException('Invalid token');
        }
        
        //TODO: get the matching provider user type
        $providerUserType = $this->getDoctrine()->getRepository('UserBundle:ProviderUserType')->find(1);
        
        // create temporary 10 character password
        $temporaryPassword = \substr(SecurityHelper::hash_sha256(time()), 0, 10);
        
        // create a provider user
        $providerUser = new ProviderUser();
        $providerUser->setProvider($invitation->getProvider());
        $providerUser->setProviderUserType($providerUserType);
        $providerUser->setEmail($invitation->getEmail());
        $providerUser->setPassword($temporaryPassword);
        $providerUser->setFirstName($invitation->getFirstName());
        $providerUser->setMiddleName($invitation->getMiddleName());
        $providerUser->setLastName($invitation->getLastName());
        $providerUser->setStatus(SiteUser::STATUS_ACTIVE);
        $this->get('services.provider_user')->create($providerUser);
        
        // create event regarding provider user creation
        $event = new CreateProviderUserEvent($providerUser);
        $event->setTemporaryPassword($temporaryPassword);
        $event->setUsedInvitation($invitation);
        
        // dispatch the event
        $this->get('event_dispatcher')->dispatch(UserEvents::ON_CREATE_PROVIDER_USER, $event);
        
        // login to provider
        $this->get('services.provider_user')->login($providerUser->getEmail(), $temporaryPassword);

        // redirect to provider homepage        
        $this->get('session')->setFlash('flash.notice', 'You have successfuly accepted the invitation.');
        
        return $this->redirect($this->generateUrl('provider_homepage'));
    }
    
}