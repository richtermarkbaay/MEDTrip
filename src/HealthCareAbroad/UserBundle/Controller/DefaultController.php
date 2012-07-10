<?php

namespace HealthCareAbroad\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Guzzle\Http\Client;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UserBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function testAction()
    {
        $client = new \Guzzle\Service\Client('http://accounts.chromedia.com/app_dev.php');
        $request = $client->post('',
            array('Authorization' => 'Bearer xxxxx'),
            array(
                'account_id' => 2, 
                'd' => 'eyJlbWFpbCI6ImNocmlzLnZlbGFyZGVAY2hyb21lZGlhLmNvbSIsImZpcnN0X25hbWUiOiJBbGxlam8gQ2hyaXMiLCJsYXN0X25hbWUiOiJWZWxhcmRlIiwicGFzc3dvcmQiOiJhNDBlOWQ3YzkxNDdhODg2M2I2ZmNkMDczODNiYjJhODBkM2U5MWZkM2E2MmI1Mzk3NGRkMjBmN2Q4ZjM4YmUzIn0='
            ));
        try {
            $request->send();
        }catch(exception $e) {
            echo get_class($e);
        }
         
        //echo $r->getStatusCode();
        exit;
        
    }
}
