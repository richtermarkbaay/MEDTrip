<?php

namespace HealthCareAbroad\ProviderBundle\Controller;

use Assetic\Exception\Exception;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use ChromediaUtilities\Helpers\SecurityHelper;

class ProviderController extends Controller
{
	public function createAction(Request $request)
    {
    	$user = new ProviderUser();
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
				
				//validate email if already exist in providerUser
				$email = $this->get('services.user')->find(array('email' => $data["form"]["email"]),  array('limit' => 1));
				
				if (count($email) > 0) {
					$this->get('session')->setFlash('flash.notice', "Email already registered!");
				}
				else {
					
					//create provider
					$provider = $this->get('services.provider')->createProvider($data["providerName"],$data["description"],$data["description"]);	
					
					//TODO: get the matching provider user type
					$providerUserType = $this->getDoctrine()->getRepository('UserBundle:ProviderUserType')->find('1');
					
					// create temporary 10 character password
					$temporaryPassword = \substr(SecurityHelper::hash_sha256(time()), 0, 10);
					 
					// create a provider user and accounts on global
	 				$user->setProvider($provider);
	 				$user->setProviderUserType($providerUserType);
	 				$user->setPassword($temporaryPassword);
	 				$user->setStatus(SiteUser::STATUS_ACTIVE);
	 				
	 				
	 				//call service to create provider user by ProviderUser
					$providerUser = $this->get('services.provider_user')->create($user);	
					if ( count($providerUser) > 0 ) {
						
						$sendingResult = $this->get('services.invitation')->sendProviderUserLoginCredentials($user,$temporaryPassword);
						if ($sendingResult) {
		                    $this->get('session')->setFlash('flash.notice', "Invitation sent to {$user->getEmail()}");
		                }
		                else {
		                    $this->get('session')->setFlash('flash.notice', "Failed to send invitation to {$user->getEmail()}");
		                }
		                
		                return $this->redirect($this->generateUrl('provider_homepage'));
						
					}
					
				}		
			}
		}
		return $this->render('ProviderBundle:Provider:create.html.twig', array(
            'form' => $form->createView(),
        ));
	}
	
}
?>