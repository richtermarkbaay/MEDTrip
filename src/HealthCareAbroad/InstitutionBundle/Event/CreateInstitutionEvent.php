<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Symfony\Component\EventDispatcher\Event;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class CreateInstitutionEvent extends Event
{
    protected $order;
	protected $institutionUser;
    
    public function __construct(Institution $institution, InstitutionUser $institutionUser)
    {
        $this->institution = $institution;
        $this->institutionUser = $institutionUser;
    }

    public function getInstitution()
    {
        return $this->institution;
    }
    
    public function getInstitutionUser()
    {
    	return $this->institutionUser;
    }
}