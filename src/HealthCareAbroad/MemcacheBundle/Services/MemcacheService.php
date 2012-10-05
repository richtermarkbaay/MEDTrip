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
     * Update the latest version of the key
     * @param string $key
     * @param string $newKeyVersion
     */
    private function _updateVersionKey($key, $newKeyVersion)
    {
        if (false === ($latestKeyVersions = $this->memcache->get(self::MEMCACHE_LATEST_VERSIONS_KEY))){
            $latestKeyVersions = array();
        }
        $latestKeyVersions[$key] = $newKeyVersion;
        $this->memcache->set(self::MEMCACHE_LATEST_VERSIONS_KEY, $latestKeyVersions);
    }
    
    /**
     * Get the latest version of the key $key
     * 
     * @param string $key
     */
    private function _getLatestVersionKey($key)
    {
        if (false === ($latestKeyVersions = $this->memcache->get(self::MEMCACHE_LATEST_VERSIONS_KEY))){
            
            return false;
        }
        
        return $latestKeyVersions[$key];
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

        // add a timestamp to the key for new version of key
        $newKey = $key.'_'.time();

        // update the latest version for this key
        $this->_updateVersionKey($key, $newKey);

        // save the value to memcache
        $this->memcache->set($newKey, $value);
        
        return $this;
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
        
        return $this->memcache->get($this->_getLatestVersionKey($key));
    }
}
