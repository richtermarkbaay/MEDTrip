<?php

namespace HealthCareAbroad\InstitutionBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class InstitutionGlobalAwardExtraValueDataTransformer implements DataTransformerInterface
{
    const YEAR_ACQUIRED_JSON_KEY = 'year_acquired';
    
    private $defaultValue = array();
    
    public function __construct()
    {
        $this->defaultValue = array(
            self::YEAR_ACQUIRED_JSON_KEY => array() // an array of years when this award was acquired
        );
    }
    
    /**
     * Transform submitted extraValue data into formatted form
     * 
     * @see Symfony\Component\Form.DataTransformerInterface::transform()
     */
    public function transform($value)
    {
        
        /**
         * NOTE: currently received value is in a comma-separated string of years. 
         * We can set the initial format upon rendering into a JSON string in future implementation
         */
        
        $years = \preg_split('/,\s*/', $value, PREG_SPLIT_NO_EMPTY);
        
        
        
        return $value;
    }
    
    public function reverseTransform($value)
    {
        $value = \stripslashes($value);
        $jsonValue = \json_decode($value, true);
        
        if (!$jsonValue) {
            $jsonValue = $this->defaultValue;
        }
        
        return \json_encode($jsonValue);
    }
}