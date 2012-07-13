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
    public function indexAction($name)
    {
        return $this->render('ProviderBundle:Default:index.html.twig', array('name' => $name));
    }
        
    public function inviteAction()
    {
        $provider = $this->getDoctrine()->getRepository('ProviderBundle:Provider')
        	->find(1);
    
        $providerUserInvitation = new ProviderUserInvitation();   
        $providerUserInvitation->setProvider($provider);
        $providerUserInvitation->setDateCreated(new \DateTime('now'));
        $form = $this->createFormBuilder($providerUserInvitation)
//        		->add('provider', 'text')
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
        	 $em = $this->getDoctrine()
                ->getEntityManager();
                
            	$em->persist($providerUserInvitation);
            	$em->flush();
            	
// 				$user = $this->get('user_service')->createUser($user->getEmail());
				
// 				if (!$user) {
// 					// invalid credentials
// 					
// 						$this->get('session')->setFlash('blogger-notice', 'Unable to send Invitation.');          
//             			
//             			return $this->redirect($this->generateUrl('ProviderBundle_invite'));
// 				}
// 				else {
// 					
// 						$this->get('session')->setFlash('blogger-notice', 'Invitation email sent successfully!');          
//             			
//             			return $this->redirect($this->generateUrl('ProviderBundle_invite'));
// 				}
					$message = \Swift_Message::newInstance()
     				->setSubject('Hello Email')
        			->setFrom('chaztine.blance@chromedia.com')
        			->setTo($provider->getEmail())
      				->setBody($this->renderView('ProviderBundle:Email:invite.email.twig', array('name' => $provider->getEmail())));
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
