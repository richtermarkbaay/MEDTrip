<?php
/*
 * @author Alnie Jacobe
 */

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\UserBundle\Form\InstitutionUserFormType;

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
				$this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_INVITATION, $event);
				
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
	public function signUpAction(Request $request)
	{
		$form = $this->createForm(new InstitutionType(), new Institution());
		$userForm = $this->createForm(new InstitutionUserFormType(), new InstitutionUser());
		
		if ($request->isMethod('POST')) {
            
            $form->bind($request);
            $userForm->bind($request);
            
            if ($form->isValid() && $userForm->isValid()) {
            	
                $institution = $this->get('services.institution')->create($form->getData());
                
                
            	if(!$institution instanceof Institution) {
           	    	
           	    	//TODO:: send notification to hca admin
           	    	$this->get('session')->setFlash('notice', "Unable to create account.");
           	    	
           	    	return $this->render('InstitutionBundle:Institution:signUp.html.twig', array(
           	    			'form' => $form->createView()
           	    	));
           	    }
           	    
           	    // set values for institutionUser
           	    $user = $userForm->getData();
           	    $user->setPassword($userForm->get('password')->getData());
           	    $user->setInstitution($institution);
           	    $user->setStatus(SiteUser::STATUS_ACTIVE);
           	    
           	    // dispatch create institution event
           	    $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION, 
                    $this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION, array('institution' => $institution, 'institutionUser' => $user)
                ));
           	    
           	    $this->get('session')->setFlash('success', "Successfully created account to HealthCareaAbroad");
           	    
           	    //login to institution
           	    $loginOk = $this->get('services.institution_user')->login($user->getEmail(), $userForm->get('password')->getData());
           	    if ($loginOk) {
           	        
           	        return $this->redirect($this->generateUrl('institution_edit_information'));
           	    }
           	    
           	    return $this->redirect($this->generateUrl('institution_login'));
            }
		}
		
		return $this->render('InstitutionBundle:Institution:signUp.html.twig', array(
			'form' => $form->createView(),
            'userForm' => $userForm->createView()
		));
	}
	
	
}
?>