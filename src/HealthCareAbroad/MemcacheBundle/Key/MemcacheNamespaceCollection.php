<?php
namespace HealthCareAbroad\MemcacheBundle\Key;

use HealthCareAbroad\MemcacheBundle\Exception\MemcacheCollectionException;

class MemcacheNamespaceCollection
{
    private $collection = array();
    
    public function set($id, MemcacheNamespace $value)
    {
        $this->collection[$id] = $value;
        
        return $this;
    }
    
    public function get($id)
    {
        if (!$this->has($id) || !$this->collection[$id] instanceof MemcacheNamespace){
            throw MemcacheCollectionException::invalidNamespace($id);
        }
        
        return $this->collection[$id];
    }
    
    public function has($id)
    {
        return \array_key_exists($id, $this->collection);
    }
}
