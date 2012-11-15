<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class AwardingBodiesEvent extends BaseEvent
{
    public function getAwardingBodies()
    {
        return $this->getData();
    }
}