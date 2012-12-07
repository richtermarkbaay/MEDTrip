<?php

namespace HealthCareAbroad\HelperBundle\Classes;

abstract class CommonArrayAccess implements \ArrayAccess
{
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }
    
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new \RuntimeException("Offset '$offset' does not exist !");
        }
    
        var_dump($offset);
        
        return $this->data[$offset];
    }
    
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }
    
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}