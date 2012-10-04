<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionTreatmentEvent extends BaseEvent
{
    public function getInstitutionTreatment()
    {
        return $this->data;
    }
}