<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionTreatmentProcedureEvent extends BaseEvent
{
    public function getInstitutionTreatmentProcedure()
    {
        return $this->data;
    }
}