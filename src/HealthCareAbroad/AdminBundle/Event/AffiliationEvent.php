<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class AffiliationEvent extends BaseEvent
{
    public function getAffiliation()
    {
        return $this->getData();
    }
}