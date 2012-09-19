<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class CountryEvent extends BaseEvent
{
    public function getCountry()
    {
        return $this->getData();
    }
}