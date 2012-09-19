<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class MedicalProcedureEvent extends BaseEvent
{
    public function getMedicalProcedure()
    {
        return $this->getData();
    }
}