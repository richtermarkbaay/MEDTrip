<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class OfferedServiceEvent extends BaseEvent
{
    public function getOfferedService()
    {
        return $this->getData();
    }
}