<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class MedicalProcedureTypeEvent extends BaseEvent
{
    public function getMedicalProcedureType()
    {
        return $this->getData();
    }
}