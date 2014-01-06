<?php 
/**
 * Frontend memcache keys
 * 
 * @author Adelbert D. Silla
 *
 */
namespace HealthCareAbroad\FrontendBundle\Services;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementTypes;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType;

class FrontendMemcacheKeys
{
    // Institution/InstitutionMedicalCenter Profile Keys
    const INSTITUTION_PROFILE_KEY = 'frontend.controller.institution_profile.{ID}';
    const INSTITUTION_MEDICAL_CENTER_PROFILE_KEY = 'frontend.controller.institutionMedicalCenter:profile.{ID}';

    // ADS Memcache Keys
    const HOMEPAGE_PREMIER_ADS_KEY = 'frontend.homepage_premier_ads';
    const HOMEPAGE_FEATURED_CLINICS_ADS_KEY = 'frontend.homepage_featured_clinics_ads';
    const HOMEPAGE_FEATURED_DESTINATIONS_ADS_KEY = 'frontend.homepage_featured_destinations_ads';
    const HOMEPAGE_FEATURED_POSTS_ADS_KEY = 'frontend.homepage_featured_posts_ads';
    const HOMEPAGE_FEATURED_VIDEO_ADS_KEY = 'frontend.homepage_featured_video_ads';
    const HOMEPAGE_COMMON_TREATMENTS_ADS_KEY = 'frontend.homepage_common_treatments_ads';
    
    const SEARCH_RESULTS_BLOG_POSTS_ADS_KEY = 'frontend.search_results_blog_posts_ads';

    const SEARCH_RESULTS_CITY_FEATURED_ADS_KEY = 'frontend.search_results_city_featured_ads.{CITY_ID}';
    const SEARCH_RESULTS_COUNTRY_FEATURED_ADS_KEY = 'frontend.search_results_country_featured_ads.{COUNTRY_ID}';
    const SEARCH_RESULTS_SPECIALIZATION_FEATURED_ADS_KEY = 'frontend.search_results_specialization_featured_ads.{SPECIALIZATION_ID}';
    const SEARCH_RESULTS_SUBSPECIALIZATION_FEATURED_ADS_KEY = 'frontend.search_results_subspecialization_featured_ads.{SUBSPECIALIZATION_ID}';
    const SEARCH_RESULTS_TREATMENT_FEATURED_ADS_KEY = 'frontend.search_results_treatment_featured_ads.{TREATMENT_ID}';
    const SEARCH_RESULTS_CITY_SPECIALIZATION_FEATURED_ADS_KEY = 'frontend.search_results_city_specialization_featured_ads.{CITY_ID}.{SPECIALIZATION_ID}';
    const SEARCH_RESULTS_CITY_SUBSPECIALIZATION_FEATURED_ADS_KEY = 'frontend.search_results_city_subspecialization_featured_ads.{CITY_ID}.{SUBSPECIALIZATION_ID}';
    const SEARCH_RESULTS_CITY_TREATMENT_FEATURED_ADS_KEY = 'frontend.search_results_city_treatment_featured_ads.{CITY_ID}.{TREATMENT_ID}';
    const SEARCH_RESULTS_COUNTRY_SPECIALIZATION_ADS_KEY = 'frontend.search_results_country_specialization_featured_ads.{COUNTRY_ID}.{SPECIALIZATION_ID}';
    const SEARCH_RESULTS_COUNTRY_SUBSPECIALIZATION_ADS_KEY = 'frontend.search_results_country_subspecialization_featured_ads.{COUNTRY_ID}.{SUBSPECIALIZATION_ID}';
    const SEARCH_RESULTS_COUNTRY_TREATMENT_ADS_KEY = 'frontend.search_results_country_treatment_featured_ads.{COUNTRY_ID}.{TREATMENT_ID}';
    
    const SEARCH_RESULTS_IMAGE_ADS_KEY = 'frontend.search_results_image_ads';
}