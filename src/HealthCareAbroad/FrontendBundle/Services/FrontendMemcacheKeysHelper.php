<?php 
/**
 * Frontend generate memcache keys helper functions
 * 
 * @author Adelbert D. Silla
 *
 */
namespace HealthCareAbroad\FrontendBundle\Services;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementTypes;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType;

class FrontendMemcacheKeysHelper
{
    static function generateInsitutionProfileKey($instititionId = '')
    {
        return str_replace('{ID}', $instititionId, FrontendMemcacheKeys::INSTITUTION_PROFILE_KEY);
    }

    static function generateInsitutionMedicalCenterProfileKey($centerId = '')
    {
        return str_replace('{ID}', $centerId, FrontendMemcacheKeys::INSTITUTION_MEDICAL_CENTER_PROFILE_KEY);
    }


    // ADS MEMCACHE KEYS FUNCTIONS 
    static function generateSearchResultsCityFeaturedAdsKey($cityId)
    {
        return str_replace('{CITY_ID}', $cityId, FrontendMemcacheKeys::SEARCH_RESULTS_CITY_FEATURED_ADS_KEY);
    }

    static function generateSearchResultsCountryFeaturedAdsKey($countryId)
    {
        return str_replace('{COUNTRY_ID}', $countryId, FrontendMemcacheKeys::SEARCH_RESULTS_COUNTRY_FEATURED_ADS_KEY);
    }

    static function generateSearchResultsSpecializationFeaturedAdsKey($specializationId)
    {
        return str_replace('{SPECIALIZATION_ID}', $specializationId, FrontendMemcacheKeys::SEARCH_RESULTS_SPECIALIZATION_FEATURED_ADS_KEY);
    }

    static function generateSearchResultsSubSpecializationFeaturedAdsKey($subSpecializationId)
    {    
        return str_replace('{SUBSPECIALIZATION_ID}', $subSpecializationId, FrontendMemcacheKeys::SEARCH_RESULTS_SUBSPECIALIZATION_FEATURED_ADS_KEY);
    }

    static function generateSearchResultsTreatmentFeaturedAdsKey($treatmentId)
    {
        return str_replace('{TREATMENT_ID}', $treatmentId, FrontendMemcacheKeys::SEARCH_RESULTS_TREATMENT_FEATURED_ADS_KEY);
    }

    static function generateSearchResultsCitySpecializationFeaturedAdsKey($cityId, $specializationId)
    {
        $searchKeys = array('{CITY_ID}', '{SPECIALIZATION_ID}');
        $replaceWith = array($cityId, $specializationId);
    
        return str_replace($searchKeys, $replaceWith, FrontendMemcacheKeys::SEARCH_RESULTS_CITY_SPECIALIZATION_FEATURED_ADS_KEY);
    }

    static function generateSearchResultsCitySubSpecializationFeaturedAdsKey($cityId, $subSpecializationId)
    {
        $searchKeys = array('{CITY_ID}', '{SUBSPECIALIZATION_ID}');
        $replaceWith = array($cityId, $subSpecializationId);

        return str_replace($searchKeys, $replaceWith, FrontendMemcacheKeys::SEARCH_RESULTS_CITY_SUBSPECIALIZATION_FEATURED_ADS_KEY);
    }

    static function generateSearchResultsCityTreatmentFeaturedAdsKey($cityId, $treatmentId)
    {
        $searchKeys = array('{CITY_ID}', '{TREATMENT_ID}');
        $replaceWith = array($cityId, $treatmentId);

        return str_replace($searchKeys, $replaceWith, FrontendMemcacheKeys::SEARCH_RESULTS_CITY_TREATMENT_FEATURED_ADS_KEY);
    }
    
    static function generateSearchResultsCountrySpecializationFeaturedAdsKey($countryId, $specializationId)
    {
        $searchKeys = array('{COUNTRY_ID}', '{SPECIALIZATION_ID}');
        $replaceWith = array($countryId, $specializationId);

        return str_replace($searchKeys, $replaceWith, FrontendMemcacheKeys::SEARCH_RESULTS_COUNTRY_SPECIALIZATION_ADS_KEY);
    }

    static function generateSearchResultsCountrySubSpecializationFeaturedAdsKey($countryId, $subSpecializationId)
    {
        $searchKeys = array('{COUNTRY_ID}', '{SUBSPECIALIZATION_ID}');
        $replaceWith = array($countryId, $subSpecializationId);
    
        return str_replace($searchKeys, $replaceWith, FrontendMemcacheKeys::SEARCH_RESULTS_COUNTRY_SUBSPECIALIZATION_ADS_KEY);
    }
    
    static function generateSearchResultsCountryTreatmentFeaturedAdsKey($countryId, $treatmentId)
    {
        $searchKeys = array('{COUNTRY_ID}', '{TREATMENT_ID}');
        $replaceWith = array($countryId, $treatmentId);

        return str_replace($searchKeys, $replaceWith, FrontendMemcacheKeys::SEARCH_RESULTS_COUNTRY_TREATMENT_ADS_KEY);
    }

    /** 
     * @param AdvertisementType or integer $advertisementType
     * @return Ambigous <NULL, string>
     */
    static function getAdvertisementKeyByType($advertisementType)
    {
        $type = (int)($advertisementType instanceof AdvertisementType ? $advertisementType->getId() : $advertisementType);

        $advertisementTypesKeys = array(
            AdvertisementTypes::HOMEPAGE_PREMIER => FrontendMemcacheKeys::HOMEPAGE_PREMIER_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_CLINIC => FrontendMemcacheKeys::HOMEPAGE_FEATURED_CLINICS_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_DESTINATION => FrontendMemcacheKeys::HOMEPAGE_FEATURED_DESTINATIONS_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_POST => FrontendMemcacheKeys::HOMEPAGE_FEATURED_POSTS_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_VIDEO => FrontendMemcacheKeys::HOMEPAGE_FEATURED_VIDEO_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_COMMON_TREATMENT => FrontendMemcacheKeys::HOMEPAGE_COMMON_TREATMENTS_ADS_KEY
        );
        
        return isset($advertisementTypesKeys[$type]) ? $advertisementTypesKeys[$type] : null; 
    }
}