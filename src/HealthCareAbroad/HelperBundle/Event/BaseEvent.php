<?php
/**
 * Base event class
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Event;

use Symfony\Component\HttpFoundation\ParameterBag;

use Symfony\Component\EventDispatcher\Event;

abstract class BaseEvent extends Event
{
    protected $data;
    
    /**
     * @var ParameterBag
     */
    private $options;
    
    public function __construct()
    {
        $this->options = new ParameterBag();
    }
    
    final public function setData($data)
    {
        $this->data = $data;
        
        return $this;
    }
    
    final public function getData()
    {
        return $this->data; 
    }
    
    /**
     * @return ParameterBag
     */
    final public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Add an option for this event
     * 
     * @param string $key
     * @param mixed $value
     */
    final public function addOption($key, $value)
    {
        $this->options->add(array($key => $value));
    }
    
    final public function getOption($key, $default=null)
    {
        return $this->options->get($key, $default);
    }
}