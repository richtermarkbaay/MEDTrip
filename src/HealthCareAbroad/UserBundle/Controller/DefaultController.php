<?php

namespace HealthCareAbroad\UserBundle\Controller;

use HealthCareAbroad\ProviderBundle\Entity\ProviderUser;

use HealthCareAbroad\ProviderBundle\Entity\Provider;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\FormViewInterface;

use Guzzle\Common\Event;

use Guzzle\Http\Message\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Guzzle\Service\Client;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UserBundle:Default:index.html.twig');
    }
    
    public function testAction()
    {
        /**$url = 'http://accounts.chromedia.com/app_dev.php';
        $post_data = array(
            'account_id' => 2, 
            'd' => 'eyJlbWFpbCI6ImNocmlzLnZlbGFyZGVAY2hyb21lZGlhLmNvbSIsImZpcnN0X25hbWUiOiJBbGxlam8gQ2hyaXMiLCJsYXN0X25hbWUiOiJWZWxhcmRlIiwicGFzc3dvcmQiOiJhNDBlOWQ3YzkxNDdhODg2M2I2ZmNkMDczODNiYjJhODBkM2U5MWZkM2E2MmI1Mzk3NGRkMjBmN2Q4ZjM4YmUzIn0='
        );
        $client = new \Guzzle\Service\Client();
        
        $client->getEventDispatcher()->addListener('request.error', function(\Guzzle\Common\Event $event){
            
            $event->stopPropagation();
            
        });
        $request = $client->post($url, null, $post_data);

        $response = $request->send();**/
        
        $user = new ProviderUser();
        
        $user_service = $this->get('user_service');
        $user_service->createUser($user);
        
        exit;
        
    }
    
     public function loginAction()
    {
        // create a task and give it some dummy data for this example
        $user = new ProviderUser();

        $form = $this->createFormBuilder($user)
            ->add('email', 'email', array('property_path'=> false))
            ->add('password', 'password', array('property_path'=> false))
            ->getForm();
     	
     	$request = $this->getRequest();
		if ($request->getMethod() == 'POST') 
		{
			   $form->bindRequest($request);
		
        	if ($form->isValid())
        	{

				$user = $this->get('services.user_service')->findByEmailAndPassword($email, $password);
				if (!$user) {
					// invalid credentials
						$this->get('session')->setFlash('blogger-notice', 'Email and Password is invalid.');          
            			return $this->redirect($this->generateUrl('UserBundle_login'));
				}
				else {
					
						$this->get('session')->setFlash('blogger-notice', 'Login successfully!');          
            			return $this->redirect($this->generateUrl('UserBundle_homepage'));
				}
				
       		 }
  	 	}
		
        	return $this->render('UserBundle:Default:user.login.twig', array(
        	    'form' => $form->createView(),
       		));             
        
    }
}
