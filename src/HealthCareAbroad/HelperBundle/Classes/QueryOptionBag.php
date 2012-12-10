<?php

/**
 * This class will be used to keep options that will be used in a query. See HealthCareAbroad\HelperBundle\Classes\QueryOption for common keys
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Classes;

use \Exception;

class QueryOptionBag
{
    private $bag = array();
    
    public function __construct(array $data=array())
    {
        $this->bag = $data;
    }

    /**
     * Add a value to this bag
     * 
     * @param string $key
     * @param Mixed $value
     * @return \HealthCareAbroad\HelperBundle\Classes\QueryOptionBag
     */
    public function add($key, $value)
    {
        $this->bag[$key] = $value;
        return $this;
    }
    
    public function get($key, $default=null)
    {
        if (!$this->has($key)) {
            return $default;
        }
        
        return $this->bag[$key];
    }
    
    public function has($key)
    {
        return \array_key_exists($key, $this->bag);
    }
    
    public function all()
    {
        return $this->bag;
    }
}


