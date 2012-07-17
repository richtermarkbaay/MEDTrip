<?php

namespace HealthCareAbroad\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;

class TokenController extends Controller
{

	public function confirmedTokenInvitationAction($token)
    {    	
     	$value = $this->get('services.token')->validate($token);	
     	
     	if(count($value) > 0){
     		return $this->render('ProviderBundle:Token:confirmedTokenInvitation.html.twig', array('token' => $token));
     	}			
		else{
			//prompt error
			echo "failed";exit;
		}
		return $this->render('ProviderBundle:Token:confirmedTokenInvitation.html.twig', array('token' => $token));
    }
	
    public function createAction(Request $request)
    {
    	$providerInvitation = new ProviderInvitation();
        $form = $this->createFormBuilder($providerInvitation)
            ->add('name', 'text')
            ->add('email', 'text')
            ->getForm();
            
    	$request = $this->getRequest();
    		
    	if ($request->getMethod() == 'POST'){
			$form->bindRequest($request);
			
			if ($form->isValid()){
				$data = $request->request->all();
				$name = $data['form']['name'];
				$email = $data['form']['email'];
				
				$dateNow = new \DateTime('now');
				$expirationDate = $dateNow->modify('+6 days');
				$generatedToken = $this->get('services.invitation')->createInvitationToken($dateNow);				
				$message = \Swift_Message::newInstance()
 					->setSubject('Activate your account with HealthCareAbroad')
 					->setFrom('alnie.jacobe@chromedia.com')
 					->setTo($email)
 					->setBody($this->renderView('ProviderBundle:Email:providerInvitationEmail.html.twig', array(
 								'name' => $name,
 								'expirationDate' => $expirationDate,
 					 			'email' => $email,
 					 			'token' => $generatedToken)));
 				
 				$this->get('mailer')->send($message);
 				$this->get('services.invitation')->createProviderInvitation($email,$message, $name);
				
				return new Response('Created token! and send invitation token to recipient');
			}
			
		}
    	return $this->render('ProviderBundle:Token:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }    
    
}