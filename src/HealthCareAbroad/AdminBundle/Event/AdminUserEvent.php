<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class AdminUserEvent extends BaseEvent
{
    public function getAdminUser()
    {
        return $this->getData();
    }
}