<?php
namespace HealthCareAbroad\UserBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionUserEvent extends BaseEvent
{
    public function getInstitutionUser()
    {
        return $this->data;
    }
}