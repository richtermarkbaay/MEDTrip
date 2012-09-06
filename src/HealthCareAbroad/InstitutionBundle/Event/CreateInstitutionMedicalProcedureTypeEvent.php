<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType;

use Symfony\Component\EventDispatcher\Event;

class CreateInstitutionMedicalProcedureTypeEvent extends Event
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