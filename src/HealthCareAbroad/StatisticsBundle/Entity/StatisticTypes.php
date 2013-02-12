<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\StatisticsBundle\Entity;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

final class StatisticTypes
{
    // ads
    const ADVERTISEMENT = 1;
    
    // hospital or institution
    const INSTITUTION = 2;
    
    // listing
    const INSTITUTION_MEDICAL_CENTER = 3;
    
    private static $types = array();
    
    private static $trackerClasses = array();
    
    static public function isValidType($type) 
    {
        return \array_key_exists($type, self::$types);    
    }
    
    static public function getTypes()
    {
        return static::$types;
    }
    
    static public function getTrackerClasses()
    {
        return static::$trackerClasses;
    }
    
    static public function _initializeTypes()
    {
        static::$types = array(
            self::ADVERTISEMENT => 'Advertisement',
            self::INSTITUTION => 'Institution',
            self::INSTITUTION_MEDICAL_CENTER
        );
        
        static::$trackerClasses = array(
            StatisticTypes::ADVERTISEMENT => '\HealthCareAbroad\StatisticsBundle\Services\Trackers\AdvertisementTracker',
            StatisticTypes::INSTITUTION => '\HealthCareAbroad\StatisticsBundle\Services\Trackers\InstitutionTracker',
            StatisticTypes::INSTITUTION_MEDICAL_CENTER => '\HealthCareAbroad\StatisticsBundle\Services\Trackers\InstitutionMedicalCenterTracker',
        );
    }
}

StatisticTypes::_initializeTypes();