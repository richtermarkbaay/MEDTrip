<?php
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionUserTypeEvent extends BaseEvent
{
    public function getInstitutionUserType()
    {
        return $this->data;
    }
}