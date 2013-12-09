<?php
namespace HealthCareAbroad\StatisticsBundle\Entity;

final class StatisticCategories
{
    const ADVERTISEMENT_IMPRESSIONS = 1;
    
    const ADVERTISEMENT_CLICKTHROUGHS = 2;
    
    const SEARCH_RESULTS_PAGE_ITEM_CLICKTHROUGHS = 3;
    
    const SEARCH_RESULTS_PAGE_ITEM_IMPRESSIONS = 4;

    const HOSPITAL_FULL_PAGE_VIEW = 5;

    const CLINIC_FULL_PAGE_VIEW = 6;
    
    
    static function getAdsCategories()
    {
        
    }
    
    static function getInstitutionCategories()
    {
        return array(
            self::HOSPITAL_FULL_PAGE_VIEW => 'Hospital/Single clinic Full Page View'
        );
    }
    
    static function getInstitutionMedicalCenterCategories()
    {
        return array(
            self::CLINIC_FULL_PAGE_VIEW => 'Clinic Full Page View'
        );
    }
}