<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class AdvertisementEvent extends BaseEvent
{
    public function getAdvertisement()
    {
        return $this->getData();
    }
}