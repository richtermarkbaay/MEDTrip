<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation;

class TokenController extends Controller
{

	public function confirmInvitationTokenAction($token)
    {    	 
     	$invitation = $this->get('services.token')->getActiveInstitutionInvitationByToken($token);	
     	
    	if (!$invitation) {
            throw $this->createNotFoundException('Invalid token');
        }
        $this->get('session')->setFlash('success', "Successfully confirm token!");
		return $this->render('InstitutionBundle:Token:confirmInvitationToken.html.twig', array('token' => $token));
    }
	
    public function createAction(Request $request)
    {
    	$institutionInvitation = new InstitutionInvitation();
        $form = $this->createFormBuilder($institutionInvitation)
            ->add('name', 'text')
            ->add('email', 'text')
            ->getForm();
            
    	$request = $this->getRequest();
    		
    	if ($request->getMethod() == 'POST') {
			$form->bindRequest($request);	
			
			if ($form->isValid()){
				
				//generate token
				$invitationToken = $this->get('services.invitation')->createInvitationToken(0);	
				
				//send institution invitation email
				$message = \Swift_Message::newInstance()
 					->setSubject('Activate your account with HealthCareAbroad')
 					->setFrom('alnie.jacobe@chromedia.com')
 					->setTo($institutionInvitation->getEmail())
 					->setBody($this->renderView('InstitutionBundle:Email:institutionInvitation.html.twig', array(
 								'name' => $institutionInvitation->getName(),
 								'expirationDate' => $invitationToken->getExpirationDate(),
 					 			'email' => $institutionInvitation->getEmail(),
 					 			'token' => $invitationToken->getToken()
 							)));
 				$sendingResult = $this->get('mailer')->send($message);
 				
 				if ($sendingResult) {
 					
 					//create institution invitation
 					$institutionInvitation = $this->get('services.invitation')->createInstitutionInvitation($institutionInvitation, $message, $invitationToken);
 					
 					// failed to save
 					if (!$institutionInvitation) {
 						return $this->_errorResponse(500, 'Exception encountered upon persisting data.');
 					}
 					$this->get('session')->setFlash('success', "Invitation sent to ".$institutionInvitation->getEmail());
 					
 				}
 				else {
 					$this->get('session')->setFlash('error', "Failed to send invitation to ".$institutionInvitation->getEmail());
 				}
			}
		}
    	return $this->render('InstitutionBundle:Token:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }    
    
}