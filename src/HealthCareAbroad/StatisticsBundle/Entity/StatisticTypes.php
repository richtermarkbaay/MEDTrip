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
    
    // clinic statistics
    const INSTITUTION_MEDICAL_CENTER = 3;
    
    // statistic type for items appearing as search results
    const SEARCH_RESULT_ITEM = 4;
    
    private static $types = array();
    
    private static $trackerClasses = array();

    private static $typesByRoutes = array();

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

    static public function getTypeByRoute($route)
    {
        return isset(static::$typesByRoutes[$route]) ? static::$typesByRoutes[$route] : null;
    }

    static public function _initializeTypes()
    {
        static::$types = array(
            self::ADVERTISEMENT => 'Advertisement',
            self::INSTITUTION => 'Institution',
            self::INSTITUTION_MEDICAL_CENTER => 'Institution Medical Center',
            self::SEARCH_RESULT_ITEM => 'Search Results Item'
        );
        
        static::$trackerClasses = array(
            StatisticTypes::ADVERTISEMENT => '\HealthCareAbroad\StatisticsBundle\Services\Trackers\AdvertisementTracker',
            StatisticTypes::INSTITUTION => '\HealthCareAbroad\StatisticsBundle\Services\Trackers\InstitutionTracker',
            StatisticTypes::INSTITUTION_MEDICAL_CENTER => '\HealthCareAbroad\StatisticsBundle\Services\Trackers\InstitutionMedicalCenterTracker',
            StatisticTypes::SEARCH_RESULT_ITEM => '\HealthCareAbroad\StatisticsBundle\Services\Trackers\SearchResultItemTracker',
        );
        
        static::$typesByRoutes = array(
            'frontend_institution_multipleCenter_profile' => self::INSTITUTION,
            'frontend_institution_singleCenter_profile' => self::INSTITUTION,
            'frontend_institutionMedicalCenter_profile' => self::INSTITUTION_MEDICAL_CENTER
        );
    }
    
}

StatisticTypes::_initializeTypes();