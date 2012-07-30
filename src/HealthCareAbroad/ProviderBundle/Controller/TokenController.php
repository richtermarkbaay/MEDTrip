<?php

namespace HealthCareAbroad\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;

class TokenController extends Controller
{

	public function confirmInvitationTokenAction($token)
    {    	
     	$invitation = $this->get('services.token')->getActiveProviderInvitationByToken($token);	
     	
    	if (!$invitation) {
            throw $this->createNotFoundException('Invalid token');
        }
		return $this->render('ProviderBundle:Token:confirmInvitationToken.html.twig', array('token' => $token));
    }
	
    public function createAction(Request $request)
    {
    	$providerInvitation = new ProviderInvitation();
        $form = $this->createFormBuilder($providerInvitation)
            ->add('name', 'text')
            ->add('email', 'text')
            ->getForm();
            
    	$request = $this->getRequest();
    		
    	if ($request->getMethod() == 'POST') {
			$form->bindRequest($request);	
			
			if ($form->isValid()){
				
				//generate token
				$invitationToken = $this->get('services.invitation')->createInvitationToken(0);	
				
				//send provider invitation email
				$message = \Swift_Message::newInstance()
 					->setSubject('Activate your account with HealthCareAbroad')
 					->setFrom('alnie.jacobe@chromedia.com')
 					->setTo($providerInvitation->getEmail())
 					->setBody($this->renderView('ProviderBundle:Email:providerInvitationEmail.html.twig', array(
 								'name' => $providerInvitation->getName(),
 								'expirationDate' => $invitationToken->getExpirationDate(),
 					 			'email' => $providerInvitation->getEmail(),
 					 			'token' => $invitationToken->getToken()
 							)));
 				$sendingResult = $this->get('mailer')->send($message);
 				
 				if ($sendingResult) {
 					
 					//create provider invitation
 					$providerInvitation = $this->get('services.invitation')->createProviderInvitation($providerInvitation, $message, $invitationToken);
 					
 					// failed to save
 					if (!$providerInvitation) {
 						return $this->_errorResponse(500, 'Exception encountered upon persisting data.');
 					}
 					$this->get('session')->setFlash('flash.notice', "Invitation sent to ".$providerInvitation->getEmail());
 					
 				}
 				else {
 					$this->get('session')->setFlash('flash.notice', "Failed to send invitation to ".$providerInvitation->getEmail());
 				}
			}
		}
    	return $this->render('ProviderBundle:Token:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }    
    
}