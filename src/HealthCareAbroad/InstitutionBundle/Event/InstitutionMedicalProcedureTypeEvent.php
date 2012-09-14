<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionMedicalProcedureTypeEvent extends BaseEvent
{
    public function getInstitutionMedicalProcedureType()
    {
        return $this->data;
    }
}