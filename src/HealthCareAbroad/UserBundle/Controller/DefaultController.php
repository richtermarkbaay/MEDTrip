<?php

namespace HealthCareAbroad\UserBundle\Controller;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

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
        $providerUserType = $this->getDoctrine()->getRepository('UserBundle:ProviderUserType')->find(1);

        
        $user = new ProviderUser();
        $user->setEmail('alnie.jacobe@chromedia.com');
        $user->setPassword(\ChromediaUtilities\Helpers\SecurityHelper::hash_sha256('123456'));// hash first the password
        $user->setFirstName('Alnie');
        $user->setMiddleName('');
        $user->setLastName('Jacobe');
        $user->setProvider($provider);
        $user->setProviderUserType($providerUserType);
        $user->setStatus(SiteUser::STATUS_ACTIVE);
        
//         $user_service = $this->get('services.user');
        $this->get('services.provider_user')->create($user);
        
        //$user_service->findByEmailAndPassword('chris.velarde@chromedia.com', \ChromediaUtilities\Helpers\SecurityHelper::hash_sha256('123456'));
        
        // sample for creating admin user
//         $adminUserType = $this->getDoctrine()->getRepository('UserBundle:AdminUserType')->find(1);
        
//         $user = new AdminUser();
//         $user->setEmail('hazel.caballero@chromedia.com');
//         $user->setPassword(\ChromediaUtilities\Helpers\SecurityHelper::hash_sha256('123456'));// hash first the password
//         $user->setFirstName('Hazel');
//         $user->setMiddleName('D');
//         $user->setLastName('Caballero');
//         $user->setStatus(SiteUser::STATUS_ACTIVE);
//         $user->setAdminUserType($adminUserType);
        
//         $this->get('services.admin_user')->create($user);
        
        exit;
        
    }
 
}
