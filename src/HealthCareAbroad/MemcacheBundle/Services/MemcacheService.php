<?php

namespace HealthCareAbroad\MemcacheBundle\Services;

class MemcacheService
{
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
        if (\class_exists('Memcache')){
            $this->memcache = new \Memcache();
            foreach ($servers as $key => $value) {
                $this->memcache->addServer($value['host'], $value['port']);
                // try connecting to server
                try {
                    $this->memcache->connect($value['host'], $value['port']);
                    $this->hasMemcache = true;
                }
                catch (\Exception $e){
                    // failed to connect to this memcache server
                }
            }
        }
        else {
            // no Memcache class
            $this->hasMemcache = false;
        }
    }

    public function getMemcache()
    {
        return $this->memcache;
    }

    public function getExtendedStats()
    {
        return $this->memcache->getExtendedStats();
    }

    public function flush()
    {
        $this->memcache->flush();
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
    public function set($key, $value, $expire = null)
    {
        if (!$this->hasMemcache) {

            return false;
        }

        // save the value to memcache
        $this->memcache->set($key, $value, null, $expire);

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
