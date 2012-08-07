<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Assetic\Exception\Exception;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use ChromediaUtilities\Helpers\SecurityHelper;

class InstitutionController extends Controller
{
	public function signUpAction()
	{
		$user = new InstitutionUser();
		$form = $this->createFormBuilder()
		
		->add('email', 'text')
		->add('firstName', 'text')
		->add('middleName', 'text')
		->add('lastName', 'text')
		->getForm();
		
		if($this->getRequest()->isMethod('POST'))
		{
			echo "check";
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
					$this->get('session')->setFlash('flash.notice', "Email already registered!");
				}
				else {
					
					//create institution
					$institution = $this->get('services.institution')->createInstitution($data["institutionName"],$data["description"],$data["description"]);	
					
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
		                    $this->get('session')->setFlash('flash.notice', "Invitation sent to {$user->getEmail()}");
		                }
		                else {
		                    $this->get('session')->setFlash('flash.notice', "Failed to send invitation to {$user->getEmail()}");
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