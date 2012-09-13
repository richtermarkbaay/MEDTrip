<?php
/**
 * Base event class
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Event;

use Symfony\Component\EventDispatcher\Event;

abstract class BaseEvent extends Event
{
    protected $data;
    
    final public function setData($data)
    {
        $this->data = $data;
        
        return $this;
    }
    
    final public function getData()
    {
        return $this->data; 
    }
}