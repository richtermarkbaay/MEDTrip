<?php

namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionUserInvitationEvent extends BaseEvent
{
    public function getInstitutionUserInvitation()
    {
        return $this->getData();
    }
}