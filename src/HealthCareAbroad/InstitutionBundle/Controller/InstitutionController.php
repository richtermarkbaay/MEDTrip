<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvents;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionInvitationType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\HelperBundle\Services\LocationService;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;
use HealthCareAbroad\UserBundle\Entity\SiteUser;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
	
class InstitutionController extends Controller
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
			if ($form->isValid()){
				
				//send institution invitation
				$sendingResult = $this->get('services.invitation')->sendInstitutionInvitation($invitation);
				
				if ($sendingResult) {
					$this->get('session')->setFlash('success', "Invitation sent to ".$invitation->getEmail());
				}
				else {
					$this->get('session')->setFlash('error', "Failed to send invitation to ".$invitation->getEmail());
				}	
			}
		}
		return $this->render('InstitutionBundle:Token:create.html.twig', array(
				'form' => $form->createView(),
		));
	}
	/**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTIONS')")
     */
	public function editInstitutionAction()
	{
		$institutionId = $this->getRequest()->get('institutionId', null);
		
		if (!$institutionId){
			// no account id in parameter, editing currently logged in account
			$session = $this->getRequest()->getSession();
			$institutionId = $session->get('institutionId');
		}
		
		//TODO: get the matching institution
		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);
		if (!$institution) {
			throw $this->createNotFoundException('Cannot update invalid account.');
		}
		
		//render data to template      
		$form = $this->createForm(new InstitutionDetailType(), $institution);
		
		//update institution details
		if ($this->getRequest()->isMethod('POST')) {
			
			$form->bindRequest($this->getRequest());
			
			if ($form->isValid()) {
				
				$institution = $this->get('services.institution')->updateInstitution($institution);
				$this->get('session')->setFlash('notice', "Successfully updated account");
			}
		}
		return $this->render('InstitutionBundle:Institution:editInstitution.html.twig', array(
				'form' => $form->createView(),
				'institution' => $institution
		));
		
	}
	/**
	 * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
	 *
	 */
	public function loadCitiesAction($countryId)
	{
		$data = $this->get('services.location')->getListActiveCitiesByCountryId($countryId);
	
		$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');
	
		return $response;
	}
	/**
	 * register institutions
	 */
	public function signUpAction()
	{
		$form = $this->createForm(new InstitutionType());
		
		if ($this->getRequest()->isMethod('POST')) {
            
            $form->bindRequest($this->getRequest());
            
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
           	    
           	    //create institution
           	    $institution = $this->get('services.institution')->createInstitution($institution);
           	    if(!$institution) {
           	    	
           	    	//TODO:: send notification to hca admin
           	    	$this->get('session')->setFlash('failed', "Unable to create account.");
           	    	return $this->render('InstitutionBundle:Institution:signUp.html.twig', array(
           	    			'form' => $form->createView(),
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
           	    $event = new CreateInstitutionEvent($institution, $user);
           	    $this->get('event_dispatcher')->dispatch(InstitutionEvents::ON_CREATE_INSTITUTION, $event);
           	    	
           	    $this->get('session')->setFlash('success', "Successfully created account to HealthCareaAbroad");
           	    
           	    //login to institution
           	    $this->get('services.institution_user')->login($user->getEmail(), $form->get('new_password')->getData());
           	    
           	    return $this->redirect($this->generateUrl('institution_edit_information'));
            }
		}
		return $this->render('InstitutionBundle:Institution:signUp.html.twig', array(
				'form' => $form->createView(),
		));
	}
	
	
}
?>