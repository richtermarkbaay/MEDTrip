<?php

namespace HealthCareAbroad\InstitutionBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionUserRoleEvent;	

class InstitutionUserRoleLogListener
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
       
    public function onDelete(CreateInstitutionUserRoleEvent $event){
    	 
    }
    
    public function onEdit(CreateInstitutionUserRoleEvent $event){
    	
    }
    
    public function onAdd(CreateInstitutionUserRoleEvent $event)
    {
    	
    }
   
    
}