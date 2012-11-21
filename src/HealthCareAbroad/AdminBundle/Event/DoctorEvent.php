<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class DoctorEvent extends BaseEvent
{
    public function getDoctor()
    {
        return $this->getData();
    }
}