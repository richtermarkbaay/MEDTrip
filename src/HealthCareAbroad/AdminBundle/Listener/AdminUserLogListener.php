<?php

namespace HealthCareAbroad\AdminBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\AdminBundle\Events\CreateAdminUserEvent;	

class AdminUserLogListener
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
       
    public function onDelete(CreateAdminUserEvent $event){
    	 
    }
    
    public function onEdit(CreateAdminUserEvent $event){
    	
    }
    
    public function onAdd(CreateAdminUserEvent $event)
    {
    	
    }
   
    
}