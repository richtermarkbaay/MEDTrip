<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType;

abstract class InstitutionMedicalProcedureTypeEvent extends Event
{
    protected $procedureType;
    
    public function __construct(InstitutionMedicalProcedureType $procedureType)
    {
        $this->procedureType = $procedureType;
        
    }

    public function getInstitutionMedicalProcedureType()
    {
        return $this->procedureType;
    }
}