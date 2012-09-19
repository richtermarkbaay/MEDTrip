<?php

namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionUserEvent extends BaseEvent
{
    public function getInstitutionUser()
    {
        return $this->getData();
    }
}