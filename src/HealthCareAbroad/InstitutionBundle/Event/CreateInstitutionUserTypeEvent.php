<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\UserBundle\Entity\InstitutionUserType;

use Symfony\Component\EventDispatcher\Event;

class CreateInstitutionUserTypeEvent extends Event
{
    protected $userType;
    
    public function __construct(InstitutionUserType $userType)
    {
        $this->userType = $userType;
    }

    public function getInstitutionUserType()
    {
        return $this->userType;
    }
    
}