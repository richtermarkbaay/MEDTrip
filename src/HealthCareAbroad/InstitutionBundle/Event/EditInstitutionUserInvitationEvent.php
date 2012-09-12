<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;

use Symfony\Component\EventDispatcher\Event;

class EditInstitutionUserInvitationEvent extends Event
{
    protected $invitation;
    
    public function __construct(InstitutionUserInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function getInstitutionUserInvitation()
    {
    	
        return $this->invitation;
    }
}