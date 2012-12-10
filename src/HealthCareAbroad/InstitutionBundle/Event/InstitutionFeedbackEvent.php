<?php

namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionFeedbackEvent extends BaseEvent
{
    public function getInstitutionUser()
    {
        return $this->getData();
    }
}