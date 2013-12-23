<?php 

namespace HealthCareAbroad\FrontendBundle\Services;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementTypes;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType;

class FrontendMemcacheKeysHelper
{
    // ADS Memcache Keys
    const HOMEPAGE_PREMIER_ADS_KEY = 'frontend.homepage_premier_ads';
    const HOMEPAGE_FEATURED_CLINICS_ADS_KEY = 'frontend.homepage_featured_clinics_ads';
    const HOMEPAGE_FEATURED_DESTINATIONS_ADS_KEY = 'frontend.homepage_featured_destinations_ads';
    const HOMEPAGE_FEATURED_POSTS_ADS_KEY = 'frontend.homepage_featured_posts_ads';
    const HOMEPAGE_FEATURED_VIDEO_ADS_KEY = 'frontend.homepage_featured_video_ads';
    const HOMEPAGE_COMMON_TREATMENTS_ADS_KEY = 'frontend.homepage_common_treatments_ads';
    
    const SEARCH_RESULTS_BLOG_POSTS_ADS_KEY = 'frontend.search_results_blog_posts_ads';

    const SEARCH_RESULTS_CITY_FEATURED_ADS_KEY = 'frontend.search_results_city_featured_ads.{CITY_ID}';
    const SEARCH_RESULTS_CITY_SPECIALIZATION_FEATURED_ADS_KEY = 'frontend.search_results_city_specialization_featured_ads.{CITY_ID}.{SPECIALIZATION_ID}';
    const SEARCH_RESULTS_CITY_SUBSPECIALIZATION_FEATURED_ADS_KEY = 'frontend.search_results_city_subspecialization_featured_ads.{CITY_ID}.{SUBSPECIALIZATION_ID}';
    const SEARCH_RESULTS_CITY_TREATMENT_FEATURED_ADS_KEY = 'frontend.search_results_city_treatment_featured_ads.{CITY_ID}.{TREATMENT_ID}';

    const SEARCH_RESULTS_COUNTRY_FEATURED_ADS_KEY = 'frontend.search_results_country_featured_ads.{COUNTRY_ID}';
    const SEARCH_RESULTS_COUNTRY_SPECIALIZATION_ADS_KEY = 'frontend.search_results_country_specialization_featured_ads.{COUNTRY_ID}.{SPECIALIZATION_ID}';
    const SEARCH_RESULTS_COUNTRY_SUBSPECIALIZATION_ADS_KEY = 'frontend.search_results_country_subspecialization_featured_ads.{COUNTRY_ID}.{SUBSPECIALIZATION_ID}';
    const SEARCH_RESULTS_COUNTRY_TREATMENT_ADS_KEY = 'frontend.search_results_country_treatment_featured_ads.{COUNTRY_ID}.{TREATMENT_ID}';

    // Institution/InstitutionMedicalCenter Profile Keys
    const INSTITUTION_PROFILE_KEY = 'frontend.controller.institution_profile.{ID}';
    const INSTITUTION_MEDICAL_CENTER_PROFILE_KEY = 'frontend.controller.institutionMedicalCenter:profile.{ID}';

    static function generateInsitutionProfileKey($instititionId = '')
    {
        return str_replace('{ID}', $instititionId, self::INSTITUTION_PROFILE_KEY);
    }

    static function generateInsitutionMedicalCenterProfileKey($centerId = '')
    {
        return str_replace('{ID}', $centerId, self::INSTITUTION_MEDICAL_CENTER_PROFILE_KEY);
    }
    
    
    
    // ADS MEMCACHE KEYS FUNCTIONS 
    static function generateSearchResultsCityFeaturedAdsKey($cityId)
    {
        return str_replace('{CITY_ID}', $cityId, self::SEARCH_RESULTS_CITY_FEATURED_ADS_KEY);
    }
    
    static function generateSearchResultsCitySpecializationFeaturedAdsKey($cityId, $specializationId)
    {
        $searchKeys = array('{CITY_ID}', '{SPECIALIZATION_ID}');
        $replaceWith = array($cityId, $specializationId);

        return str_replace($searchKeys, $replaceWith, self::SEARCH_RESULTS_CITY_SPECIALIZATION_FEATURED_ADS_KEY);
    }

    static function generateSearchResultsCitySubSpecializationFeaturedAdsKey($cityId, $subSpecializationId)
    {
        $searchKeys = array('{CITY_ID}', '{SUBSPECIALIZATION_ID}');
        $replaceWith = array($cityId, $subSpecializationId);

        return str_replace($searchKeys, $replaceWith, self::SEARCH_RESULTS_CITY_SUBSPECIALIZATION_FEATURED_ADS_KEY);
    }

    static function generateSearchResultsCityTreatmentFeaturedAdsKey($cityId, $treatmentId)
    {
        $searchKeys = array('{CITY_ID}', '{TREATMENT_ID}');
        $replaceWith = array($cityId, $treatmentId);

        return str_replace($searchKeys, $replaceWith, self::SEARCH_RESULTS_CITY_TREATMENT_FEATURED_ADS_KEY);
    }

    static function generateSearchResultsCountryFeaturedAdsKey($countryId)
    {
        return str_replace('{COUNTRY_ID}', $countryId, self::SEARCH_RESULTS_COUNTRY_FEATURED_ADS_KEY);
    }
    
    static function generateSearchResultsCountrySpecializationFeaturedAdsKey($countryId, $specializationId)
    {
        $searchKeys = array('{COUNTRY_ID}', '{SPECIALIZATION_ID}');
        $replaceWith = array($countryId, $specializationId);

        return str_replace($searchKeys, $replaceWith, self::SEARCH_RESULTS_COUNTRY_SPECIALIZATION_ADS_KEY);
    }

    static function generateSearchResultsCountrySubSpecializationFeaturedAdsKey($countryId, $subSpecializationId)
    {
        $searchKeys = array('{COUNTRY_ID}', '{SUBSPECIALIZATION_ID}');
        $replaceWith = array($countryId, $subSpecializationId);
    
        return str_replace($searchKeys, $replaceWith, self::SEARCH_RESULTS_COUNTRY_SUBSPECIALIZATION_ADS_KEY);
    }
    
    static function generateSearchResultsCountryTreatmentFeaturedAdsKey($countryId, $treatmentId)
    {
        $searchKeys = array('{COUNTRY_ID}', '{TREATMENT_ID}');
        $replaceWith = array($countryId, $treatmentId);

        return str_replace($searchKeys, $replaceWith, self::SEARCH_RESULTS_COUNTRY_TREATMENT_ADS_KEY);
    }

    /** 
     * @param AdvertisementType or integer $advertisementType
     * @return Ambigous <NULL, string>
     */
    static function getAdvertisementKeyByType($advertisementType)
    {
        $type = (int)($advertisementType instanceof AdvertisementType ? $advertisementType->getId() : $advertisementType);

        $advertisementTypesKeys = array(
            AdvertisementTypes::HOMEPAGE_PREMIER => self::HOMEPAGE_PREMIER_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_CLINIC => self::HOMEPAGE_FEATURED_CLINICS_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_DESTINATION => self::HOMEPAGE_FEATURED_DESTINATIONS_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_POST => self::HOMEPAGE_FEATURED_POSTS_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_FEATURED_VIDEO => self::HOMEPAGE_FEATURED_VIDEO_ADS_KEY,
            AdvertisementTypes::HOMEPAGE_COMMON_TREATMENT => self::HOMEPAGE_COMMON_TREATMENTS_ADS_KEY
        );
        
        return isset($advertisementTypesKeys[$type]) ? $advertisementTypesKeys[$type] : null; 
    }

}