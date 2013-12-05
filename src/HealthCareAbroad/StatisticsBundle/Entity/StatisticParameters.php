<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\StatisticsBundle\Entity;

use HealthCareAbroad\StatisticsBundle\Services\StatisticsParameterBag;

final class StatisticParameters
{
    const TYPE = 'type';
    
    const CATEGORY_ID = 'category_id';
    
    const ADVERTISEMENT_ID = 'advertisement_id';
    
    const INSTITUTION_ID = 'institution_id';
    
    const INSTITUTION_MEDICAL_CENTER_ID = 'institution_medical_center_id';
    
    const COUNTRY_ID = 'country_id';
    
    const CITY_ID = 'city_id';
    
    const SPECIALIZATION_ID = 'specialization_id';
    
    const SUB_SPECIALIZATION_ID = 'sub_specialization_id';
    
    const TREATMENT_ID = 'treatment_id';
    
    const IP_ADDRESS = 'ip_address';
    
    /**
     * Helper function to encode statistic parameters
     * @param array $parameters
     */
    static public function encodeParameters(array $parameters)
    {
        return \base64_encode(\serialize($parameters));
    }
    
    /**
     * 
     * @param string $encodedParameters
     * @return StatisticsParameterBag
     */
    static public function decodeParameters($encodedParameters, $asParameterBag = true)
    {
        $decodedParameters = \unserialize(\base64_decode($encodedParameters));
        if ($asParameterBag) {
            $decodedParameters = new StatisticsParameterBag($decodedParameters);
        }
        
        return $decodedParameters;
    }
}