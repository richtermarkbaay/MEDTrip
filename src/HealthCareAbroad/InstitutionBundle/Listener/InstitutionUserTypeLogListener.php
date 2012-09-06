<?php

namespace HealthCareAbroad\InstitutionBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionUserTypeEvent;	

class InstitutionUserTypeLogListener
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
       
    public function onDelete(CreateInstitutionUserTypeEvent $event){
    	 
    }
    
    public function onEdit(CreateInstitutionUserTypeEvent $event){
    	
    }
    
    public function onAdd(CreateInstitutionUserTypeEvent $event)
    {
    	
    }
   
    
}