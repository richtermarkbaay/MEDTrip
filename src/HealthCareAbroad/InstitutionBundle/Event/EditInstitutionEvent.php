<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Symfony\Component\EventDispatcher\Event;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class EditInstitutionEvent extends Event
{
    protected $institution;
	protected $institutionUser;
    
    public function __construct(Institution $institution)
    {
        $this->institution = $institution;
        
    }

    public function getInstitution()
    {
        return $this->institution;
    }
    
    public function getInstitutionUser()
    {
    	return $this->institutionUser;
    }
    public function setInstitutionUser(InstitutionUser $user)
    {
    	$this->institutionUser = $user;
    }
}