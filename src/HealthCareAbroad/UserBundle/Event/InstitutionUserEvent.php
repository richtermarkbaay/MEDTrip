<?php
namespace HealthCareAbroad\UserBundle\Event;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Symfony\Component\EventDispatcher\Event;

abstract class InstitutionUserEvent extends Event
{
    protected $institutionUser;
    
    
    public function __construct(InstitutionUser $user)
    {
        $this->institutionUser = $user;
    }
    
    public function getInstitutionUser()
    {
        return $this->institutionUser;
    }
}