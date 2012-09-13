<?php
/**
 * Log listener for InstitutionUser events
 * 
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\LogBundle\Listener\Institution;

use HealthCareAbroad\LogBundle\Listener\BaseListener;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

use HealthCareAbroad\UserBundle\Event\InstitutionUserEvent;

class InstitutionUserListener extends BaseListener
{
    public function onLogin(InstitutionUserEvent $event)
    {
        
    }
    
    public function onAdd(BaseEvent $event)
    {
        
    }
    
    public function onEdit(BaseEvent $event)
    {
    
    }
    
    public function onDelete(BaseEvent $event)
    {
        
    }
}