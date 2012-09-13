<?php
/*
 * @author Alnie Jacobe
 */

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionInvitationEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionInvitationEvents;
use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvents;


use HealthCareAbroad\InstitutionBundle\Form\InstitutionType;
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
	
class InstitutionSignUpController  extends Controller
{
	
	/**
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
				
				// create event on invite institution and dispatch
				$event = new CreateInstitutionInvitationEvent($invitation);
				$this->get('event_dispatcher')->dispatch(InstitutionInvitationEvents::ON_ADD_INSTITUTION_INVITATION, $event);
				
				if ($sendingResult) {
					$this->get('session')->setFlash('success', "Invitation sent to ".$invitation->getEmail());
				}
				else {
					$this->get('session')->setFlash('error', "Failed to send invitation to ".$invitation->getEmail());
				}	
			}
		}
		
		return $this->render('InstitutionBundle:Token:create.html.twig', array(
				'form' => $form->createView()
		));
	}
	
	/**
	 * register/create institutions
	 */
	public function signUpAction()
	{
		$form = $this->createForm(new InstitutionType());
		$request = $this->getRequest();
		
		if ($request->isMethod('POST')) {
            
            $form->bindRequest($request);
            
            if ($form->isValid()) {
            	
            	//create institution
           	    $institution = new Institution();
           	    $institution->setName($form->get('name')->getData());
           	    $institution->setDescription($form->get('description')->getData());
           	    $institution->setSlug('test');
           	    $institution->setStatus(SiteUser::STATUS_ACTIVE);
           	    $institution->setAddress1($form->get('address1')->getData());
           	    $institution->setAddress2($form->get('address2')->getData());
           	    $institution->setLogo('logo.jpg');
           	    $institution->setCity($form->get('city')->getData());
           	    $institution->setCountry($form->get('country')->getData());
           	    
           	    $institution = $this->get('services.institution')->createInstitution($institution);
           	    if(!$institution) {
           	    	
           	    	//TODO:: send notification to hca admin
           	    	$this->get('session')->setFlash('failed', "Unable to create account.");
           	    	
           	    	return $this->render('InstitutionBundle:Institution:signUp.html.twig', array(
           	    			'form' => $form->createView()
           	    	));
           	    }
           	    
           	    // set values for institutionUser
           	    $user = new InstitutionUser();
           	    $user->setInstitution($institution);
           	    $user->setFirstName($form->get('firstName')->getData());
        	    $user->setMiddleName($form->get('middleName')->getData());
           	    $user->setLastName($form->get('lastName')->getData());
           	    $user->setPassword($form->get('new_password')->getData());
           	    $user->setEmail($form->get('email')->getData());
           	    $user->setStatus(SiteUser::STATUS_ACTIVE);
           	    
           	    // create Institution event and dispatch
           	    $event = new CreateInstitutionEvent($institution);
           	    $event->setInstitutionUser($user);
           	    $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION, $event);
           	    	
           	    $this->get('session')->setFlash('success', "Successfully created account to HealthCareaAbroad");
           	    
           	    //login to institution
           	    $loginOk = $this->get('services.institution_user')->login($user->getEmail(), $form->get('new_password')->getData());
           	    if ($loginOk) {
           	        
           	        return $this->redirect($this->generateUrl('institution_edit_information'));
           	    }
           	    
           	    return $this->redirect($this->generateUrl('institution_login'));
            }
		}
		
		return $this->render('InstitutionBundle:Institution:signUp.html.twig', array(
				'form' => $form->createView()
		));
	}
	
	
}
?>