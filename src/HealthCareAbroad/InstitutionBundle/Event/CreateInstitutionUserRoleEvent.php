<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\UserBundle\Entity\InstitutionUserRole;

use Symfony\Component\EventDispatcher\Event;

class CreateInstitutionUserRoleEvent extends Event
{
    protected $userRole;
    
    public function __construct(InstitutionUserRole $userRole)
    {
        $this->userRole = $userRole;
    }

    public function getInstitutionUserRole()
    {
        return $this->userRole;
    }
    
}