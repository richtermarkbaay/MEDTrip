<?php

namespace HealthCareAbroad\ProviderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormViewInterface;

use HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation;

use HealthCareAbroad\ProviderBundle\Entity\Provider;

class DefaultController extends Controller
{
    public function Accounts_Accept_Invitation($token,$id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$Invitationtoken = $this->getDoctrine()->getRepository('HelperBundle:InvitationToken')
        			->find($token);
      
    	if (!$Invitationtoken) {
            throw $this->createNotFoundException('Invalid Token.');
        }
        
        $ProviderUserInvitation = $em->getRepository('ProviderBundle:ProviderUserInvitation')
                       ->getId($id);
        
        return $this->render('BloggerBlogBundle:Blog:show.html.twig', array(
            'ProviderUserInvitation'      => $ProviderUserInvitation
        ));
    }
        
    public function inviteAction()
    {
        $provider = $this->getDoctrine()->getRepository('ProviderBundle:Provider')
        	->find(1);
    
        $providerUserInvitation = new ProviderUserInvitation();   
        $providerUserInvitation->setProvider($provider);
        $providerUserInvitation->setDateCreated(new \DateTime('now'));
        $form = $this->createFormBuilder($providerUserInvitation)
            ->add('email', 'email')
            ->add('message', 'textarea')
            ->add('firstName', 'text')
             ->add('middleName', 'text')
            ->add('lastName', 'text')
            ->getForm();
     	
     	$request = $this->getRequest();
     	
		if ($request->getMethod() == 'POST') 
		{
			$form->bindRequest($request);
			    	
        	if ($form->isValid())
        	{
        		$data = $request->request->all();
				$token = $data['form']['_token'];
			
        		$Invitationtoken = $this->getDoctrine()->getRepository('HelperBundle:InvitationToken')
        			->find($token);

        		$providerUserInvitation->setInvitationToken($Invitationtoken);
        	
        	 	$em = $this->getDoctrine()
                	->getEntityManager();                
            	$em->persist($providerUserInvitation);
            	$em->flush();

				$message = \Swift_Message::newInstance()
     				->setSubject('Provider User Invitation for Health Care Abroad')
        			->setFrom('chaztine.blance@chromedia.com')
        			->setTo($providerUserInvitation->getEmail())
      				->setBody($this->renderView('ProviderBundle:Email:invite.email.twig', array(
      					'providerUserInvitation' => $providerUserInvitation,
      					'token' => $Invitationtoken,
      					'provider' => $provider
      				)));
    				
    				$this->get('mailer')->send($message);
    				
    				$this->get('session')->setFlash('msg-notice', 'Invitation email sent successfully!');          
            				
             		return $this->redirect($this->generateUrl('ProviderBundle_invite'));
       		 }
  	 	}
		
        	return $this->render('ProviderBundle:Default:invite.html.twig', array(
        	    'form' => $form->createView(),
       		));             
        
    }
}
