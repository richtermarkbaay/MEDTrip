<?php

namespace HealthCareAbroad\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

//use HealthCareAbroad\HelperBundle\Classes\Tokenizer;
use HealthCareAbroad\HelperBundle\Entity\InvitationToken;
use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;

class TokenController extends Controller
{

	public function confirmedTokenInvitationAction($token)
    {
    	//echo $token;
    	$repository = $this->getDoctrine()
						   ->getRepository('HelperBundle:InvitationToken');
    	
		//select all token that has expired date and status is still active/1
		$query = $repository->createQueryBuilder('t')
    						->add('where', 't.token = :token and t.status != 0')
    						->setParameter('token', $token)
    						->getQuery();
    						$token = $query->getResult();
		echo $token->getId();
		exit;
		//var_dump($token = $query->getResult());exit;
		 $token[$i]->getId(); exit;
		if(count($token) !=0 )
		{
			//retrieve data esp name,email
			$product = $this->getDoctrine()
        				    ->getRepository('ProviderBundle:ProviderInvitation')
                            ->find($id);
                            
			$token[$i]->getId(); 
			// redirect sa link for creation page
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
    		
    	if ($request->getMethod() == 'POST')
		{
			$form->bindRequest($request);
			if($form->isValid())
			{
				$data = $request->request->all();
				$name = $data['form']['name'];
				$email = $data['form']['email'];
				
				$expirationDate = new \DateTime('now');
				$expirationDate->modify('+6 days');
				$generatedToken = $this->get('services.create_invitation')->createInvitationToken($expirationDate);				
				
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
 	
 				$this->get('services.create_invitation')->createProviderInvitation($email,$message, $name);
				return new Response('Created token! and send invitation token to recipient');
				// return $this->render('ProviderBundle:Token:create.html.twig', array(
 					//'form' => $form->createView(),
				//));
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