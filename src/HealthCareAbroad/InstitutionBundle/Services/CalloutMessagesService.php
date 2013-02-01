<?php

namespace HealthCareAbroad\InstitutionBundle\Services;

class CalloutMessagesService
{
    private $callouts = array(); 
    
    public function __construct($callouts=array())
    {
        $this->callouts = $callouts;
    }
    
    public function has($key)
    {
        return \array_key_exists($key, $this->callouts);
    }
    
    public function get($key, $params = array())
    {
        if (!$this->has($key)) {
            throw new \Exception("Callout message key {$key} is not defined.");
        }
        
        $callout = $this->replacePlaceholdersToValues($this->callouts[$key], $params); 
        
        return $callout;
    }
    
    public function replacePlaceholdersToValues($callout, $params)
    {
        $jsonCallout = json_encode($callout);
        $searchPlaceholders = array_keys($params);
        $replaceValues = array_values($params);

        $jsonCallout = str_replace($searchPlaceholders, $replaceValues, $jsonCallout);

        return json_decode($jsonCallout, true);
    }
}