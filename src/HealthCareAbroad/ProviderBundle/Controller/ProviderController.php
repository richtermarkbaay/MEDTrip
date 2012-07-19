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
    	$providerUser = new ProviderUser();
        $form = $this->createFormBuilder($providerUser)
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
					
					$provider = $this->get('services.provider')->createProvider($data["providerName"],$data["description"],$data["description"]);	
					
					//TODO: get the matching provider user type
					$providerUserType = $this->getDoctrine()->getRepository('UserBundle:ProviderUserType')->find('1');
					
					// create temporary 10 character password
					$temporaryPassword = \substr(SecurityHelper::hash_sha256(time()), 0, 10);
					 
					// create a provider user and accounts on global
	 				$user = new ProviderUser();
	 				$user->setProvider($provider);
	 				$user->setProviderUserType($providerUserType);
	 				$user->setEmail($data["form"]['email']);
	 				$user->setPassword($temporaryPassword);
	 				$user->setFirstName($data["form"]['firstName']);
	 				$user->setMiddleName($data["form"]['middleName']);
	 				$user->setLastName($data["form"]['lastName']);
	 				$user->setStatus(SiteUser::STATUS_ACTIVE);
	 				
	 				//call service to create provider user by ProviderUser object
					$providerUser = $this->get('services.provider_user')->create($user);	
					if ( count($providerUser) > 0 ) {
						return new Response('Successfully created an account to HealthCareAbroad.');
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