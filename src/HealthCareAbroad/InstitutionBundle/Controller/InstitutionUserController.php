<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\UserBundle\Form\UserAccountDetailType;

use HealthCareAbroad\UserBundle\Form\UserLoginType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionUserChangePasswordType;

use Symfony\Component\Validator\Constraints\NotBlank;

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
	public function settingsAction()
	{
		return $this->render('AdminBundle:Default:settings.html.twig');
	}
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
    }
    
    public function logoutAction()
    {
        $this->get('security.context')->setToken(null);
        $this->getRequest()->getSession()->invalidate();
        return $this->redirect($this->generateUrl('institution_login'));
    }
    
    public function changePasswordAction()
    {
        //get user account in chromedia global accounts by accountID
        $session = $this->getRequest()->getSession();
        $institutionUserService = $this->get('services.institution_user');
        $institutionUser = $institutionUserService->findById($session->get('accountId'));
        $form = $this->createForm(new InstitutionUserChangePasswordType(), $institutionUser);
    	
    	if ($this->getRequest()->isMethod('POST')) {
    		
    		$form->bindRequest($this->getRequest());
    		
    		if ($form->isValid()) {
    			$institutionUser->setPassword(SecurityHelper::hash_sha256($form->get('new_password')->getData()));
    			$institutionUserService->update($institutionUser);
    			
    			$this->get('session')->setFlash('success', "Password changed!");
    		}
    			
    	}
    	return $this->render('InstitutionBundle:InstitutionUser:changePassword.html.twig', array(
            'form' => $form->createView()));
    }
    
    public function editAccountAction()
    {
    	$accountId = $this->getRequest()->get('accountId', null);
        if (!$accountId){
            // no account id in parameter, editing currently logged in account
            $session = $this->getRequest()->getSession();
            $accountId = $session->get('accountId');
        }
        $institutionUser = $this->get('services.institution_user')->findById($accountId, true); //get user account in chromedia global accounts by accountID
        
        if (!$institutionUser) {
            throw $this->createNotFoundException('Cannot update invalid account.');
        }
    	$form = $this->createForm(new UserAccountDetailType(), $institutionUser);
    	
    	if ($this->getRequest()->isMethod('GET')) {
    	    $this->get('session')->set('referer', $this->getRequest()->headers->get('referer', $this->generateUrl('institution_homepage')));
        }
        elseif ($this->getRequest()->isMethod('POST')) {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $institutionUser = $this->get('services.institution_user')->update($institutionUser);
                $this->get('session')->setFlash('success', "Successfully updated account");
                $refer = $this->get('session')->get('referer');
                $this->getRequest()->getSession()->remove('referer');
                return $this->redirect($refer);
            }
        }
        
        return $this->render('InstitutionBundle:InstitutionUser:editAccount.html.twig', array(
            'form' => $form->createView(),
            'institutionUser' => $institutionUser ));
    }
    public function inviteAction()
    {
    	
        $institution = $this->get('services.institution')->getCurrentInstitution();
        $institutionUserInvitation = new InstitutionUserInvitation();
        $form = $this->createForm(new InstitutionUserInvitationType(), $institutionUserInvitation);
        
        if ($this->getRequest()->isMethod('POST')) {
            
            $form->bind($this->getRequest());
            if ($form->isValid()){
                
                $sendingResult = $this->get('services.invitation')->sendInstitutionUserInvitation($institution, $institutionUserInvitation);
                if ($sendingResult) {
                    $this->get('session')->setFlash('success', "Invitation sent to {$institutionUserInvitation->getEmail()}");
                }
                else {
                    $this->get('session')->setFlash('error', "Failed to send invitation to {$institutionUserInvitation->getEmail()}");
                }
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
        
        // create event regarding institution user creation
        $event = new CreateInstitutionUserEvent($institutionUser);
        $event->setTemporaryPassword($temporaryPassword);
        $event->setUsedInvitation($invitation);
        
        // dispatch the event
        $this->get('event_dispatcher')->dispatch(UserEvents::ON_CREATE_INSTITUTION_USER, $event);
        
        // login to institution
        $this->get('services.institution_user')->login($institutionUser->getEmail(), $temporaryPassword);

        // redirect to institution homepage        
        $this->get('session')->setFlash('success', 'You have successfuly accepted the invitation.');
        
        return $this->redirect($this->generateUrl('institution_homepage'));
    }
    
    public function viewAllAction()
    {
        $institutionService = $this->get('services.institution');
        $institution = $institutionService->getCurrentInstitution();
        
        $users = $institutionService->getAllStaffOfInstitution($institution);
        return $this->render('InstitutionBundle:InstitutionUser:viewAll.html.twig', array('users' => $users));
    }
    
}