<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionMedicalCenterEvent extends BaseEvent
{
    public function getInstitutionMedicalCenter()
    {
        return $this->data;
    }
}