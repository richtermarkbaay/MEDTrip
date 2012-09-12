<?php

namespace HealthCareAbroad\InstitutionBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalProcedureEvent;	

class InstitutionMedicalProcedureLogListener
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
       
    public function onDelete(CreateInstitutionMedicalProcedureEvent $event){
    	 
    }
    
    public function onEdit(CreateInstitutionMedicalProcedureEvent $event){
    	
    }
    
    public function onAdd(CreateInstitutionMedicalProcedureEvent $event)
    {
    	
    }
   
    
}