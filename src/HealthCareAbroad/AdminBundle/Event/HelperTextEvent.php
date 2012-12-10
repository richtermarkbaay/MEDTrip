<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class HelperTextEvent extends BaseEvent
{
    public function getHelperText()
    {
        return $this->getData();
    }
}