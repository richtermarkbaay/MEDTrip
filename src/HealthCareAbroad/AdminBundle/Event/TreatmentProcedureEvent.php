<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class TreatmentProcedureEvent extends BaseEvent
{
    public function getTreatmentProcedure()
    {
        return $this->getData();
    }
}