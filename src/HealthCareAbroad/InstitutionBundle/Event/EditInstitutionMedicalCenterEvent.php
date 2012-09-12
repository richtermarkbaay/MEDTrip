<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Symfony\Component\EventDispatcher\Event;

class EditInstitutionMedicalCenterEvent extends Event
{
    protected $medicalCenter;
    
    public function __construct(InstitutionMedicalCenter $medicalCenter)
    {
        $this->medicalCenter = $medicalCenter;
        
    }

    public function getInstitutionMedicalCenter()
    {
    	
        return $this->medicalCenter;
    }    
}