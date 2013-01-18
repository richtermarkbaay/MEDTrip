<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

class CalloutMessagesService
{
    private $callouts = array();
    
    public function __construct($callouts=array())
    {
        $this->callouts = $callouts;
    }
    
    public function has($key)
    {
        return \array_key_exists($key, $this->callouts);
    }
    
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new \Exception("Callout message key {$key} is not defined.");
        }
        
        return $this->callouts[$key];
    }
}