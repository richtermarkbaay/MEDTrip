<?php 
namespace HealthCareAbroad\AdminBundle\Events;

use HealthCareAbroad\UserBundle\Entity\AdminUserType;

use Symfony\Component\EventDispatcher\Event;

class CreateAdminUserTypeEvent extends Event
{
    protected $userType;
    
    public function __construct(AdminUserType $userType)
    {
        $this->userType = $userType;
    }

    public function getAdminUserType()
    {
        return $this->userType;
    }
    
}