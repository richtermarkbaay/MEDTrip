<?php

namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class AdminUserTypeEvent extends BaseEvent
{
    public function getAdminUserType()
    {
        return $this->getData();
    }
}