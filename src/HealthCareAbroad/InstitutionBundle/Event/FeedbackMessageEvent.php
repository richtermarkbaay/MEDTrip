<?php

namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class FeedbackMessageEvent extends BaseEvent
{
    public function getFeedbackMessage()
    {
        return $this->getData();
    }
}