<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class SubSpecializationEvent extends BaseEvent
{
    public function getSubSpecialization()
    {
        return $this->getData();
    }
}