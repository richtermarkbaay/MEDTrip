<?php 
namespace HealthCareAbroad\AdminBundle\Events;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

use Symfony\Component\EventDispatcher\Event;

class CreateAdminUserEvent extends Event
{
    protected $adminUser;
    
    public function __construct(AdminUser $adminUser)
    {
        $this->adminUser = $adminUser;
    }

    public function getAdminUser()
    {
        return $this->adminUser;
    }
    
}