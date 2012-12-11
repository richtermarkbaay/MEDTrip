<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class MedicalProviderGroupEvent extends BaseEvent
{
    public function getMedicalProviderGroup()
    {
        return $this->getData();
    }
}