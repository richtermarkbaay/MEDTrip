<?php 
/**
 * Advertisement types value is based in healthcareabroad.advertisement_types table ids
 * Note: Make sure they are the same!
 * 
 * @author Adelbert Silla
 */
namespace HealthCareAbroad\AdvertisementBundle\Services;

final class AdvertisementTypes
{
    const HOMEPAGE_PREMIER = 1;
    const HOMEPAGE_FEATURED_CLINIC = 2;
    const HOMEPAGE_FEATURED_DESTINATION = 3;
    const HOMEPAGE_FEATURED_SERVICE = 4;
    const HOMEPAGE_FEATURED_VIDEO = 5;
    const HOMEPAGE_FEATURED_POST = 6;
    const HOMEPAGE_COMMON_TREATMENT = 7;
    
    const SEARCH_RESULTS_SPECIALIZATION_FEATURE = 8;
    const SEARCH_RESULTS_SUBSPECIALIZATION_FEATURE = 9;
    const SEARCH_RESULTS_TREATMENT_FEATURE = 10;
    const SEARCH_RESULTS_COUNTRY_FEATURE = 11;
    const SEARCH_RESULTS_CITY_FEATURE = 12;

    const SEARCH_RESULTS_GLOBAL_MEDIA = 13;

    const SEARCH_RESULTS_COUNTRY_SPECIALIZATION_FEATURE = 15;
    const SEARCH_RESULTS_COUNTRY_SUBSPECIALIZATION_FEATURE = 16;
    const SEARCH_RESULTS_COUNTRY_TREATMENT_FEATURE = 17;

    const SEARCH_RESULTS_CITY_SPECIALIZATION_FEATURE = 18;
    const SEARCH_RESULTS_CITY_SUBSPECIALIZATION_FEATURE = 19;
    const SEARCH_RESULTS_CITY_TREATMENT_FEATURE = 20;
    
}