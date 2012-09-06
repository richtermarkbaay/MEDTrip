<?php

namespace HealthCareAbroad\AdminBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\AdminBundle\Events\CreateAdminUserTypeEvent;	

class AdminUserTypeLogListener
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
       
    public function onDelete(CreateAdminUserTypeEvent $event){
    	 
    }
    
    public function onEdit(CreateAdminUserTypeEvent $event){
    	
    }
    
    public function onAdd(CreateAdminUserTypeEvent $event)
    {
    	
    }
   
    
}