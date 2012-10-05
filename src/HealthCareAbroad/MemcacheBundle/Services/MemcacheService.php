<?php

namespace HealthCareAbroad\MemcacheBundle\Services;

class MemcacheService
{
    private $servers;
    
    private static $setupMemcacheComplete = false;
    
    public function __construct($servers=array())
    {
        if (!static::$setupMemcacheComplete) {
            
            $memcached = new \Memcached();
            
            static::$setupMemcacheComplete = true;
        }
        
    }
    
    public function set($key, $value)
    {
        
    }
    
    public function get($key)
    {
        
    }
}