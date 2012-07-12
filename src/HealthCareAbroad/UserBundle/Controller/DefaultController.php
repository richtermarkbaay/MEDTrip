<?php

namespace HealthCareAbroad\UserBundle\Controller;

use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use Guzzle\Common\Event;

use Guzzle\Http\Message\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Guzzle\Service\Client;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UserBundle:Default:index.html.twig', array('name' => $name));
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
        $user->setEmail('chris.velarde@chromedia.com');
        $user->setPassword('123456');// hash first the password
        $user->setFirstName('Allejo Chris');
        $user->setMiddleName('G');
        $user->setLastName('Velarde');
        
        $user_service = $this->get('services.user');
        $user_service->createUser($user);
        
        exit;
        
    }
}
