<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedure;
use Symfony\Component\EventDispatcher\Event;

class EditInstitutionMedicalProcedureEvent extends Event
{
    protected $procedure;
    
    public function __construct(InstitutionMedicalProcedure $procedure)
    {
        $this->procedure = $procedure;
        
    }

    public function getInstitutionMedicalProcedure()
    {
        return $this->procedure;
    }
   
}