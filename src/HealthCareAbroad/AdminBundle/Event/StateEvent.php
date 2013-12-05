<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class StateEvent extends BaseEvent
{
    public function getState()
    {
        return $this->getData();
    }
}