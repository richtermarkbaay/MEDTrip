<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class AwardingBodyEvent extends BaseEvent
{
    public function getAwardingBody()
    {
        return $this->getData();
    }
}