<?php
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionUserTypeRoleEvent extends BaseEvent
{
    public function getInstitutionUserTypeRole()
    {
        return $this->data;
    }
}