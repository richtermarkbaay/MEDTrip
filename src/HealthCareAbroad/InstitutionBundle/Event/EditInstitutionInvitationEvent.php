<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation;

use Symfony\Component\EventDispatcher\Event;

class EditInstitutionInvitationEvent extends Event
{
    protected $invitation;
    
    public function __construct(InstitutionInvitation $invitation)
    {
        $this->invitation = $invitation;
        
    }

    public function getInstitutionInvitation()
    {
        return $this->invitation;
    }
}