<?php

namespace HealthCareAbroad\UserBundle\Controller;

use HealthCareAbroad\UserBundle\Event\UserEvents;

use HealthCareAbroad\UserBundle\Event\CreateInstitutionUserEvent;

use HealthCareAbroad\UserBundle\Event\InstitutionUserEvents;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Guzzle\Common\Event;

use Guzzle\Http\Message\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Guzzle\Service\Client;

class DefaultController extends Controller
{
    public function testEventAction()
    {
        $invitation = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionUserInvitation')->find(3);
        
        $user = $this->get('services.institution_user')->findById(2);
        $event = new CreateInstitutionUserEvent($user);
        $event->setTemporaryPassword('wata');
        $event->setUsedInvitation($invitation);
        
        $dispatcher = $this->get('event_dispatcher')->dispatch(UserEvents::ON_CREATE_PROVIDER_USER, $event);
        
        exit;
    }
    
    
    public function testAction()
    {
//         $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find(1);
//         $institutionUserType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find(1);

        
//         $user = new InstitutionUser();
//         $user->setEmail('alnie.jacobe@chromedia.com');
//         $user->setPassword(\ChromediaUtilities\Helpers\SecurityHelper::hash_sha256('123456'));// hash first the password
//         $user->setFirstName('Alnie');
//         $user->setMiddleName('');
//         $user->setLastName('Jacobe');
//         $user->setInstitution($institution);
//         $user->setInstitutionUserType($institutionUserType);
//         $user->setStatus(SiteUser::STATUS_ACTIVE);
//         $this->get('services.institution_user')->create($user);
        
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
