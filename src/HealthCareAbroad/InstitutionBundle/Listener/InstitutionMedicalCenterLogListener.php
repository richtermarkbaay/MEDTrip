<?php

namespace HealthCareAbroad\InstitutionBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionMedicalCenterEvent;	

class InstitutionMedicalCenterLogListener
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
       
    public function onDelete(){
    	 
    }
    
    public function onEdit(){
    	
    }
    
    public function onAdd()
    {
    	
    }
   
    
}