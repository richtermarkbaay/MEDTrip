<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class MedicalCenterEvent extends BaseEvent
{
    public function getMedicalCenter()
    {
        return $this->getData();
    }
}