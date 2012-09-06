<?php

namespace HealthCareAbroad\AdminBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\AdminBundle\Events\CreateAdminUserRoleEvent;	

class AdminUserRoleLogListener
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
       
    public function onDelete(CreateAdminUserRoleEvent $event){
    	 
    }
    
    public function onEdit(CreateAdminUserRoleEvent $event){
    	
    }
    
    public function onAdd(CreateAdminUserRoleEvent $event)
    {
    	
    }
   
    
}