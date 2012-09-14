<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionMedicalProcedureEvent extends BaseEvent
{
    public function getInstitutionMedicalProcedure()
    {
        return $this->data;
    }
}