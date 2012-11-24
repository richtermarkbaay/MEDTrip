<?php
namespace HealthCareAbroad\MemcacheBundle\Key;

use HealthCareAbroad\MemcacheBundle\Exception\MemcacheCollectionException;

class MemcacheKeyCollection
{
    private $memcacheCollection = array();
    
    public function has($id)
    {
        return \array_key_exists($id, $this->memcacheCollection);
    }
    
    /**
     * @param string $id Key name
     * @return MemcacheKey
     */
    public function get($id)
    {
        if (!$this->has($id) || !$this->memcacheCollection[$id] instanceof MemcacheKey) {
            throw MemcacheCollectionException::invalidKey($id);
        }
        
        return $this->memcacheCollection[$id];
    }
    
    public function set($id, MemcacheKey $memcacheKey)
    {
        $this->memcacheCollection[$id] = $memcacheKey;
        
        return $this;
    }
}
