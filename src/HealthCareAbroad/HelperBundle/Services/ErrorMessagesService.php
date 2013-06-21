<?php

namespace HealthCareAbroad\HelperBundle\Services;

class ErrorMessagesService
{
    private $error_messages = array(); 
    
    public function __construct($error_messages=array())
    {
        $this->error_messages = $error_messages;
    }
    
    public function has($key)
    {
        return \array_key_exists($key, $this->error_messages);
    }
    
    public function get($key, $params = array())
    {
        if (!$this->has($key)) {
            throw new \Exception("Error message key {$key} is not defined.");
        }
        
        $error_messages = $this->replacePlaceholdersToValues($this->error_messages[$key], $params); 
        
        return $error_messages;
    }
    
    public function replacePlaceholdersToValues($error_messages, $params)
    {
        $jsonCallout = json_encode($error_messages);
        $searchPlaceholders = array_keys($params);
        $replaceValues = array_values($params);
    
        $jsonCallout = str_replace($searchPlaceholders, $replaceValues, $jsonCallout);
    
        return json_decode($jsonCallout, true);
    }
}