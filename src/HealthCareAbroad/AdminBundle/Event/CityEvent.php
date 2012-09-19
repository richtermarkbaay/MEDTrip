<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class CityEvent extends BaseEvent
{
    public function getCity()
    {
        return $this->getData();
    }
}