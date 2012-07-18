<?php

namespace HealthCareAbroad\ProviderBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;
use HealthCareAbroad\ProviderBundle\Entity\Provider;
use HealthCareAbroad\UserBundle\Entity\ProviderUser;
use HealthCareAbroad\UserBundle\Entity\ProviderUserType;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProviderController extends Controller
{
	public function createAction(Request $request)
    {
    	$providerInvitation = new ProviderInvitation();
        $form = $this->createFormBuilder($providerInvitation)
            ->add('email', 'text')
            ->add('name', 'text')
            ->getForm();
            
    	$request = $this->getRequest();
    	if ($request->getMethod() == 'POST') {
			$form->bindRequest($request);
			
			if ($form->isValid()) {
				
				$data = $request->request->all();
				$providerName = $data["providerName"];
				$providerDescription = $data["description"];
				$providerUserName = $data["form"]['name'];
				$providerUserEmail = $data["form"]['email'];
				
				//for provider_id
				$provider = new Provider();
				$provider->setName($providerName);
				$provider->setDescription($providerDescription);
				$provider->setSlug($providerDescription);
				$provider->setStatus(1);
				
				
				$em = $this->getDoctrine()->getEntityManager();
				$em->persist($provider);
 				$em->flush();
 				
 				$providerUserType = new ProviderUserType();
 				$providerUserType = $this->getDoctrine()->getRepository('UserBundle:ProviderUserType')->find('1');
 				//echo $providerUserType;exit;
 				$user = new ProviderUser();
 				$user->setProvider($provider);
 				$user->setProviderUserType($providerUserType);
 				$user->setStatus(1);
 				
				//call service to create provider user by ProviderUser object
				$providerUser = $this->get('services.provider_user')->create($user);	
				var_dump($providerUser);exit;
				
						
			}
		}
		return $this->render('ProviderBundle:Provider:create.html.twig', array(
            'form' => $form->createView(),
        ));
	}
	
}
?>