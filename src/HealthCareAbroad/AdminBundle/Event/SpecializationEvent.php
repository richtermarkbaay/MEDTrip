<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class SpecializationEvent extends BaseEvent
{
    public function getSpecialization()
    {
        return $this->getData();
    }
}