<?php 

namespace HealthCareAbroad\InstitutionBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation;

class CreateInstitutionInvitationEvent extends Event{
	
	
	public $invitation;
	
	public function __construct(InstitutionInvitation $invitation)
	{
		$this->invitation = $invitation;
	}
	
	public function getInvitation()
	{
		return $this->invitation;
	}
}