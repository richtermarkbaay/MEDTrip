<?php

namespace HealthCareAbroad\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\HelperBundle\Classes\Tokenizer;
use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;
class TokenController extends Controller
{
	
    public function createAction(Request $request)
    {
    	$providerInvitation = new ProviderInvitation();
        $form = $this->createFormBuilder($providerInvitation)
            ->add('name', 'text')
            ->add('email', 'text')
            ->getForm();
    	$request = $this->getRequest();	
    		
    	if ($request->getMethod() == 'POST')
		{
			$form->bindRequest($request);
			
			if($form->isValid())
			{
				$data = $request->request->all();
				$name = $data['form']['name'];
				$email = $data['form']['email'];
				
				$tokenizer = new Tokenizer();
				$generatedToken = $tokenizer->generateTokenString();
				
				$expirationDate = new \DateTime('now');
				$expirationDate->modify('+6 days');
				$status = "1";
				
				$invitationToken = new InvitationToken();
				$invitationToken->setToken($generatedToken);
				$invitationToken->setExpirationDate($expirationDate);
				$invitationToken->setStatus($status);
				$em = $this->getDoctrine()->getEntityManager();
				$em->persist($invitationToken);
				$em->flush();
				
				



				//send email
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
 	
 				$providerInvitation = new ProviderInvitation();
 				$providerInvitation->setEmail($email);
 				$providerInvitation->setMessage($message);
 				$providerInvitation->setName($name);
 				$providerInvitation->setStatus($status);
 				//$providerInvitation->setInvitationToken($generatedToken);
 				$em = $this->getDoctrine()->getEntityManager();
 				$em->persist($providerInvitation);
 				$em->flush();
				
				//$checkToken = $this->get('CheckExpirationDate')->checkExpiredDateToken();
				return new Response('Created token! and send invitation token to recipient');
			}
			return $this->render('ProviderBundle:Token:create.html.twig', array(
            	'form' => $form->createView(),
        	));
		}
    	return $this->render('ProviderBundle:Token:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }    
    
}