<?php

namespace HealthCareAbroad\InstitutionBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalProcedureTypeEvent;	

class InstitutionMedicalProcedureTypeLogListener
{
    /**
     * 
     * @var Doctrine\ORM\EntityManager
     */
    private $em;
    
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }
       
    public function onDelete(CreateInstitutionMedicalProcedureTypeEvent $event){
    	 
    }
    
    public function onEdit(CreateInstitutionMedicalProcedureTypeEvent $event){
    	
    }
    
    public function onAdd(CreateInstitutionMedicalProcedureTypeEvent $event)
    {
    	
    }
   
    
}