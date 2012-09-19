<?php 

namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionInvitationEvent extends BaseEvent
{
	public function getInvitation()
	{
		return $this->getData();
	}
}