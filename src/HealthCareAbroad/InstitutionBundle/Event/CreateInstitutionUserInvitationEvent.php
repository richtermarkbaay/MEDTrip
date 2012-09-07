<?php 

namespace HealthCareAbroad\InstitutionBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;

class CreateInstitutionUserInvitationEvent extends Event{
	
	
	public $invitation;
	
	public function __construct(InstitutionUserInvitation $invitation)
	{
		$this->invitation = $invitation;
	}
	
	public function getInvitation()
	{
		return $this->invitation;
	}
}