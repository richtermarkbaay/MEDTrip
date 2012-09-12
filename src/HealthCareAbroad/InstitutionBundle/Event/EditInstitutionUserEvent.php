<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Symfony\Component\EventDispatcher\Event;
use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

class EditInstitutionUserEvent extends Event
{
    protected $user;
    
    public function __construct(InstitutionUser $user)
    {
        $this->institution = $user;
        
    }

    public function getInstitutionUser()
    {
    	return $this->user;
    }
   
}