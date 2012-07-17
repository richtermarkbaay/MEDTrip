<?php

namespace HealthCareAbroad\UserBundle\Controller;

use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use Guzzle\Common\Event;

use Guzzle\Http\Message\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Guzzle\Service\Client;

class DefaultController extends Controller
{
    public function testAction()
    {
        $provider = $this->getDoctrine()->getRepository('ProviderBundle:Provider')->find(1);
        $providerUserType = $this->getDoctrine()->getRepository('ProviderBundle:ProviderUserType')->find(1);

        
        $user = new ProviderUser();
        $user->setEmail('chris.velarde@chromedia.com');
        $user->setPassword(\ChromediaUtilities\Helpers\SecurityHelper::hash_sha256('123456'));// hash first the password
        $user->setFirstName('Allejo Chris');
        $user->setMiddleName('G');
        $user->setLastName('Velarde');
        $user->setProvider($provider);
        $user->setProviderUserType($providerUserType);
        $user->setStatus(3);
        
        $user_service = $this->get('services.user');
        //$user_service->createProviderUser($user);
        
        $user_service->findByEmailAndPassword('chris.velarde@chromedia.com', \ChromediaUtilities\Helpers\SecurityHelper::hash_sha256('123456'));
        
        exit;
        
    }
 
}
