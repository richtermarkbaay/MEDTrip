<?php

namespace HealthCareAbroad\MemcacheBundle\Services;

class MemcacheService
{

    /**
     * Memcache key used for storing latests versions of a memcache key
     */
    const MEMCACHE_LATEST_VERSIONS_KEY = 'hca_key_latest_versions';
    
    /**
     * @var boolean if setup for Memcache client is already done
     */
    private static $setupMemcacheComplete = false;

    /**
     * @var boolean if Memcache exists
     */
    private $hasMemcache = false;

    /**
     * @var \Memcache
     */
    private $memcache;
    
    public function __construct($servers=array())
    {
        $this->hasMemcache = \class_exists('Memcache');

        if (!static::$setupMemcacheComplete && $this->hasMemcache) {
            
            $this->memcache = new \Memcache();
            foreach ($servers as $key => $value) {
                $this->memcache->addServer($value['host'], $value['port']);
            }
            static::$setupMemcacheComplete = true;
        }
        
    }
    
    /**
     * Delete an item from the memcache server
     * 
     * @param string $key
     * @return
     */
    public function delete($key)
    {
        if (!$this->hasMemcache) {
            return false;
        }
        
        return $this->memcache->delete($key);
    }
    
    /**
     * Increment the value of the given key
     * 
     * @param string $key
     * @return boolean
     */
    public function increment($key)
    {
        if (!$this->hasMemcache) {
            
            return false;
        }
        
        $this->memcache->increment($key);
        
        return true;
    }
    
    
    /**
     * Store data to memcached server. Return false if no Memcache is available.
     * 
     * @param string $key
     * @param mixed $value
     * @return boolean|\HealthCareAbroad\MemcacheBundle\Services\MemcacheService
     */
    public function set($key, $value)
    {
        if (!$this->hasMemcache) {
            
            return false;
        }
        
        // save the value to memcache
        $this->memcache->set($key, $value);
        
        return true;
    }
    
    /**
     * Get a stored value from memcached server
     * 
     * @param string $key
     * @return Mixed
     */
    public function get($key)
    {
        if (!$this->hasMemcache) {
            
            return false;
        }
        
        return $this->memcache->get($key);
    }
}
