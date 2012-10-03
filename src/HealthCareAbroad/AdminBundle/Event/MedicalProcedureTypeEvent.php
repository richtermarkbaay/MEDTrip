<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class TreatmentEvent extends BaseEvent
{
    public function getTreatment()
    {
        return $this->getData();
    }
}