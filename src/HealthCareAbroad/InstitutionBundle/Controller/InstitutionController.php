<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use Assetic\Exception\Exception;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDetailType;
use HealthCareAbroad\HelperBundle\Services\LocationService;
use HealthCareAbroad\HelperBundle\Entity\Country;
use HealthCareAbroad\UserBundle\Entity\InstitutionUser;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\UserEvents;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use ChromediaUtilities\Helpers\SecurityHelper;

class InstitutionController extends Controller
{
	public function editInstitutionAction()
	{
		
		$institutionId = $this->getRequest()->get('institutionId', null);
		echo $institutionId;exit;
		if (!$institutionId){
			// no account id in parameter, editing currently logged in account
			$session = $this->getRequest()->getSession();
			$institutionId = $session->get('institutionId');
		}
		
		//TODO: get the matching institution
		$institution = new Institution();
		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);
		
		//render data to template
		$form = $this->createForm(new InstitutionDetailType(), $institution);
		
		//update institution details
		if ($this->getRequest()->isMethod('POST')) {
			$form->bindRequest($this->getRequest());
			if ($form->isValid()) {
				$institution->setName($form->get('name')->getData());
           	    $institution->setDescription($form->get('description')->getData());
           	    $institution->setSlug('test');
           	    $institution->setStatus(SiteUser::STATUS_ACTIVE);
           	    $institution->setAddress1($form->get('address1')->getData());
           	    $institution->setAddress2($form->get('address2')->getData());
           	    $institution->setLogo('logo.jpg');
           	    $institution->setCity($form->get('city')->getData());
           	    $institution->setCountry($form->get('country')->getData());
				$institution = $this->get('services.institution')->updateInstitution($institution);
				
				if ( count($institution) > 0 ) {
					$this->get('session')->setFlash('notice', "Successfully updated account");
				}
				else
				{
					$this->get('session')->setFlash('notice', "Unable to update account!");
					
				}
			}
		}
		
		return $this->render('InstitutionBundle:Institution:editInstitution.html.twig', array(
				'form' => $form->createView(),
				'institution' => $institution
		));
		
	}
	public function loadCitiesAction($countryId)
	{
		$data = $this->get('services.location')->getListActiveCitiesByCountryId($countryId);
	
		$response = new Response(json_encode($data));
		$response->headers->set('Content-Type', 'application/json');
	
		return $response;
	}
	
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
           	    $institutionUserType = $this->get('event_dispatcher')->dispatch(UserEvents::ON_CREATE_INSTITUTION, $event);
           	    
           	    if ( count($institutionUserType) > 0 ) {
           	    	$this->get('session')->setFlash('success', "Successfully created account to HealthCareaAbroad");
           	    }
           	    else {
           	    	$this->get('session')->setFlash('error', "Failed to create account on HealthCareAbroad");
           	    }
           	    return $this->redirect($this->generateUrl('institution_login'));
            }
		}
		return $this->render('InstitutionBundle:Institution:signUp.html.twig', array(
				'form' => $form->createView(),
		));
	}
	
	
}
?>