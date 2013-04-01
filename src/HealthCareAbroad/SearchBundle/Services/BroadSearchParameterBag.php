<?php

namespace HealthCareAbroad\SearchBundle\Services;

/**
 * Dedicated parameter bag for broad search widget
 * TODO: currently not used in frontend search
 * 
 * @author Allejo Chris G. Velarde
 */
class BroadSearchParameterBag extends SearchParameterBag
{
    const STATE_HAS_TERM_ID = 1;
    
    const STATE_HAS_TERM_LABEL = 2;
    
    const STATE_HAS_DESTINATION_ID = 4;
    
    
    private static $allowedKeys = array('termLabel', 'termId', 'destinationLabel', 'destinationId');
    
    public function __construct(array $parameters=array())
    {
        $this->_initParameters($parameters);
    }
    
    private function _initParameters($parameters=array())
    {
        $this->parameters = array();
        foreach (self::$allowedKeys as $key) {
            $this->parameters[$key] = isset($parameters[$key]) && '' != \trim($parameters[$key])
            // assess if typecasting to int for numeric values is safe here
            ? \is_numeric($parameters[$key]) ? (int) $parameters[$key] : $parameters[$key]
            : null;
        }
        $state = 0;// initialize state of parameters
        
        // process the term parameters
        // we can only have 2 states from the term parameters, either having a selected termId or a typed keyword
        if ($this->parameters['termId']) {
            // for BC
            $this->parameters['treatmentId'] = $this->parameters['termId']; 
            $state += self::STATE_HAS_TERM_ID; // set state to having a selected termId
        }
        elseif ($this->parameters['termLabel']) {
            $state += self::STATE_HAS_TERM_LABEL; // set state to having a typed term label
        }
        
        // process the destinationId
        if ($this->parameters['destinationId']) {
            list($countryId, $cityId) = \explode('-', $this->parameters['destinationId']);
            $this->parameters['countryId'] = (int)$countryId;
            $this->parameters['cityId'] = (int)$cityId;
            $state += self::STATE_HAS_DESTINATION_ID; // set state to having a selected destination
        }
        $this->parameters['state'] = $state;
        
        // destinations only search
        if ($state == BroadSearchParameterBag::getStateForHasSelectedDestinationOnly()) {
            $this->parameters['context'] = SearchParameterBag::SEARCH_TYPE_DESTINATIONS;
        }
        elseif ($state == BroadSearchParameterBag::getStateForHasSelectedTermOnly() || $state == BroadSearchParameterBag::getStateForHasTypedTermLabelOnly() ) {
            $this->parameters['context'] = SearchParameterBag::SEARCH_TYPE_TREATMENTS; // treatments only search
        }
        elseif ( $state==BroadSearchParameterBag::getStateForHasSelectedTermAndSelectedDestination() || $state==BroadSearchParameterBag::getStateForHasTypedTermLabelAndSelectedDestination() ){
            $this->parameters['context'] = SearchParameterBag::SEARCH_TYPE_COMBINATION;
        }
        else {
            $this->parameters['context'] = null;// unknown state
        }
    }
    
    
    public static function getStateForHasSelectedTermOnly()
    {
        return self::STATE_HAS_TERM_ID;
    }
    
    public static function getStateForHasTypedTermLabelOnly()
    {
        return self::STATE_HAS_TERM_LABEL;
    }
    
    public static function getStateForHasSelectedDestinationOnly()
    {
        return self::STATE_HAS_DESTINATION_ID;
    }
    
    public static function getStateForHasSelectedTermAndSelectedDestination()
    {
        return self::STATE_HAS_TERM_ID + self::STATE_HAS_DESTINATION_ID;
    }
    
    public static function getStateForHasTypedTermLabelAndSelectedDestination()
    {
        return self::STATE_HAS_TERM_LABEL + self::STATE_HAS_DESTINATION_ID;
    }
    
    static public function getAllowedParameters()
    {
        return self::$allowedKeys;
    }
}