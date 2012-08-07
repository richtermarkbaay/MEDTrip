<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Assetic\Exception\Exception;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionType;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use ChromediaUtilities\Helpers\SecurityHelper;

class InstitutionController extends Controller
{
	
	public function signUpAction()
	{
		
		$form = $this->createForm(new InstitutionType());
		//getActiveCitiesByCountryId($countryId)
		
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
           	    $institution->setAddress2($form->get('address1')->getData());
           	    $institution->setLogo('logo.jpg');
           	    $institution->setCityId($form->get('city')->getData()->getId());
           	    $institution->setCountryId($form->get('country')->getData()->getId());
           	    
           	    $institution = $this->get('services.institution')->createInstitution($institution);
           	    
           	    //TODO: get the matching institution user type
           	    $institutionUserType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find('1');
           	    	
           	    // create a institution user and accounts on global
           	    $user = new InstitutionUser();
           	    $user->setInstitution($institution);
           	    $user->setInstitutionUserType($institutionUserType);
           	    $user->setFirstName($form->get('firstName')->getData());
           	    $user->setMiddleName($form->get('middleName')->getData());
           	    $user->setLastName($form->get('lastName')->getData());
           	    $user->setPassword($form->get('new_password')->getData());
           	    $user->setEmail($form->get('email')->getData());
           	    
           	    $user->setStatus(SiteUser::STATUS_ACTIVE);
           	    $institutionUser = $this->get('services.institution_user')->create($user);
           	    
           	    if ( count($institutionUser) > 0 ) {
           	    	$this->get('session')->setFlash('flash.notice', "Successfully created account to HealthCareaAbroad");
           	    }
           	    else {
           	    	$this->get('session')->setFlash('flash.notice', "Failed to create account on HealthCareAbroad");
           	    }
           	    
           	    return $this->redirect($this->generateUrl('institution_homepage'));
            }
		}
		return $this->render('InstitutionBundle:Institution:signUp.html.twig', array(
				'form' => $form->createView(),
		));
	}
	public function createAction(Request $request)
    {
    	$user = new InstitutionUser();
        $form = $this->createFormBuilder($user)
        	->add('email', 'text')
            ->add('firstName', 'text')
            ->add('middleName', 'text')
            ->add('lastName', 'text')
            ->getForm();
        
        
    	if ($this->getRequest()->isMethod('POST')) {
            
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
            	$data = $request->request->all();
				
				//validate email if already exist in institutionUser
				$email = $this->get('services.user')->find(array('email' => $data["form"]["email"]),  array('limit' => 1));
				
				if (count($email) > 0) {
					$this->get('session')->setFlash('notice', "Email already registered!");
				}
				else {
					
					//create institution
					$institution = new Institution();
					$institution->setName($data["institutionName"]);
					$institution->setDescription($data["description"]);
					$institution->setSlug($data["description"]);
					
					$institution = $this->get('services.institution')->createInstitution($institution);	
					
					//TODO: get the matching institution user type
					$institutionUserType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find('1');
					
					// create temporary 10 character password
					$temporaryPassword = \substr(SecurityHelper::hash_sha256(time()), 0, 10);
					 
					// create a institution user and accounts on global
	 				$user->setInstitution($institution);
	 				$user->setInstitutionUserType($institutionUserType);
	 				$user->setPassword($temporaryPassword);
	 				$user->setStatus(SiteUser::STATUS_ACTIVE);
	 				
	 				
	 				//call service to create institution user by InstitutionUser
					$institutionUser = $this->get('services.institution_user')->create($user);	
					if ( count($institutionUser) > 0 ) {
						
						$sendingResult = $this->get('services.invitation')->sendInstitutionUserLoginCredentials($user,$temporaryPassword);
						if ($sendingResult) {
		                    $this->get('session')->setFlash('notice', "Invitation sent to {$user->getEmail()}");
		                }
		                else {
		                    $this->get('session')->setFlash('notice', "Failed to send invitation to {$user->getEmail()}");
		                }
		                
		                return $this->redirect($this->generateUrl('institution_homepage'));
						
					}
					
				}		
			}
		}
		return $this->render('InstitutionBundle:Institution:create.html.twig', array(
            'form' => $form->createView(),
        ));
	}
	
}
?>