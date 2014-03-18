<?php
namespace HealthCareAbroad\LogBundle\Entity;

class LogEventData
{
    private $message;
    private $data;
    private $action;
    
    /**
     * 
     * @param string $v
     * @return LogEventData
     */
    public function setMessage($v)
    {
        $this->message = $v;
        
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     * 
     * @param array $v
     * @return LogEventData
     */
    public function setData(array $v=array())
    {
        $this->data = $v;
        
        return $this;
    }
    
    public function setAction($v=null)
    {
        $this->action = $v;
        
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}