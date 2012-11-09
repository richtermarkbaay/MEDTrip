<?php
/*
 * @author Alnie Jacobe
 */

namespace HealthCareAbroad\InstitutionBundle\Controller;

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
	    $institutionType = $request->get('institutionType', InstitutionTypes::MEDICAL_GROUP_NETWORK_MEMBER);
	    $factory = $this->get('services.institution.factory');
	    $institution = $factory->createByType($institutionType);
	    $form = $this->createForm(new InstitutionSignUpFormType(), $institution);
	    
	    if ($request->isMethod('POST')) {
	        $form->bind($request);
	        
	        if ($form->isValid()) {
	            
	            $institution = $form->getData();
	            
	            // initialize required database fields
	          	$institution->setAddress1('');
    			$institution->setAddress2('');
    			$institution->setContactEmail('');
    			$institution->setContactNumber('');
    			$institution->setDescription('');
    			$institution->setLogo('');
    			$institution->setCoordinates('');
    			$institution->setState('');
    			$institution->setWebsites('');
    			$institution->setStatus(InstitutionStatus::getBitValueForActiveStatus());
    			$institution->setZipCode('');
    			$factory->save($institution);
	            
	            // create Institution user
	            $institutionUser = new InstitutionUser();
	            $institutionUser->setEmail($form->get('email')->getData());
	            $institutionUser->setFirstName($institution->getName());
	            $institutionUser->setLastName('Admin');
	            $institutionUser->setPassword($form->get('password')->getData());
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
	            
	            return $this->redirect($this->generateUrl('institution_homepage'));
	        }
	    }
	    
	    return $this->render('InstitutionBundle:Institution:signUp.html.twig', array(
            'form' => $form->createView(),
            'institutionTypes' => InstitutionTypes::getList(),
            'selectedInstitutionType' => $institutionType,
        ));
	}
	
}
?>