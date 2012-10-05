<?php

namespace HealthCareAbroad\MemcacheBundle\Services;

class MemcacheService
{

    /**
     * Memcache key used for storing latests versions of a memcache key
     */
    const MEMCACHE_LATEST_VERSIONS_KEY = 'hca_key_latest_versions';

    private $servers;
    
    private static $setupMemcacheComplete = false;

    /**
     * @var boolean if Memcache exists
     */
    private $hasMemcache = false;

    
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

    private function _updateVersionKey($key, $newKeyVersion)
    {
        if (false === ($latestKeyVersions = $this->memcache->get(self::MEMCACHE_LATEST_VERSIONS_KEY))){
            $latestKeyVersions = array();
        }
        $latestKeyVersions[$key] = $newKeyVersion;
        $this->memcache->set(self::MEMCACHE_LATEST_VERSIONS_KEY, $latestKeyVersions);
    }
    
    public function set($key, $value)
    {
        if (!$this->hasMemcache) {
            return;
        }

        // add a timestamp to the key for new version of key
        $newKey = $key.'_'.time();

        // update the latest version for this key
        $this->_updateVersionKey($key, $newKey);

        // save the value to memcache
        $this->memcache->set($newKey, $value);
    }
    
    public function get($key)
    {
        
    }
}
