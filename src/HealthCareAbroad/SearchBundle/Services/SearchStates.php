<?php

namespace HealthCareAbroad\SearchBundle\Services;

use HealthCareAbroad\SearchBundle\Services\SearchStates;

use HealthCareAbroad\SearchBundle\Services\SearchUrlRoutes;


final class SearchStates
{
    const HAS_COUNTRY = 1;
    const HAS_CITY = 2;
    const HAS_SPECIALIZATION = 4;
    const HAS_SUB_SPECIALIZATION = 8;
    const HAS_TREATMENT = 16;
    const HAS_TERM_LABEL = 32;
    
    // state naming for possible single searches
    const COUNTRY_SEARCH = 'country_search';
    const CITY_SEARCH = 'city_search';
    const SPECIALIZATION_SEARCH = 'specialization_search';
    const SUB_SPECIALIZATION_SEARCH = 'sub_specialization_search';
    const TREATMENT_SEARCH = 'treatment_search';
    // state naming for possible combined searches
    const COUNTRY_SPECIALIZATION_SEARCH = 'country_specialization_search';
    const COUNTRY_SUB_SPECIALIZATION_SEARCH = 'country_sub_specialization_search';
    const COUNTRY_TREATMENT_SEARCH = 'country_treatment_search';
    const CITY_SPECIALIZATION_SEARCH = 'city_specialization_search';
    const CITY_SUB_SPECIALIZATION_SEARCH = 'city_sub_specialization_search';
    const CITY_TREATMENT_SEARCH = 'city_treatment_search';
    
    // a mapping of the possible searches and their 
    private static $searchStateValues = array();
    
    static public function getStateToRouteMapping()
    {
        return array(
            SearchStates::COUNTRY_SEARCH => SearchUrlRoutes::RESULTS_COUNTRY,
            SearchStates::CITY_SEARCH => SearchUrlRoutes::RESULTS_CITY,
            SearchStates::SPECIALIZATION_SEARCH => SearchUrlRoutes::RESULTS_SPECIALIZATION,
            SearchStates::SUB_SPECIALIZATION_SEARCH => SearchUrlRoutes::RESULTS_SUB_SPECIALIZATION,
            SearchStates::TREATMENT_SEARCH => SearchUrlRoutes::RESULTS_TREATMENT,
            SearchStates::COUNTRY_SPECIALIZATION_SEARCH => SearchUrlRoutes::RESULTS_COUNTRY_SPECIALIZATION,
            SearchStates::COUNTRY_SUB_SPECIALIZATION_SEARCH => SearchUrlRoutes::RESULTS_COUNTRY_SUB_SPECIALIZATION,
            SearchStates::COUNTRY_TREATMENT_SEARCH => SearchUrlRoutes::RESULTS_COUNTRY_TREATMENT,
            SearchStates::CITY_SPECIALIZATION_SEARCH => SearchUrlRoutes::RESULTS_CITY_SPECIALIZATION,
            SearchStates::CITY_SUB_SPECIALIZATION_SEARCH => SearchUrlRoutes::RESULTS_CITY_SUB_SPECIALIZATION,
            SearchStates::CITY_TREATMENT_SEARCH => SearchUrlRoutes::RESULTS_CITY_TREATMENT,
        );
    }
    
    /**
     * Mapping of search parameter key to search state value. 
     * This is useful in getting to total state value of the current request
     */
    static public function getSearchParameterKeyMappingToSearchStateValues()
    {
        return array(
            SearchParameterService::PARAMETER_KEY_COUNTRY_ID => SearchStates::HAS_COUNTRY,
            SearchParameterService::PARAMETER_KEY_CITY_ID => SearchStates::HAS_CITY,
            SearchParameterService::PARAMETER_KEY_SPECIALIZATION_ID => SearchStates::HAS_SPECIALIZATION,
            SearchParameterService::PARAMETER_KEY_SUB_SPECIALIZATION_ID => SearchStates::HAS_SUB_SPECIALIZATION,
            SearchParameterService::PARAMETER_KEY_TREATMENT_ID => SearchStates::HAS_TREATMENT,
        );
    }
    
    static public function getSearchUrlParameterKeyToSearchStateValueMapping()
    {
        return array(
            SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY => SearchStates::HAS_COUNTRY,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY => SearchStates::HAS_CITY,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION => SearchStates::HAS_SPECIALIZATION,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION => SearchStates::HAS_SUB_SPECIALIZATION,
            SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT => SearchStates::HAS_TREATMENT,
        );
    }
    
    static public function getStateValue($state)
    {
        return isset(self::$searchStateValues[$state])
            ? self::$searchStateValues[$state]
            : null;
    }
    
    static public function getSearchStateFromValue($value)
    {
        $byValue = \array_flip(self::$searchStateValues);
        
        return isset($byValue[$value])
            ? $byValue[$value]
            : null;
    }
    
    static public function _initiateValue()
    {
        self::$searchStateValues = array(
            SearchStates::COUNTRY_SEARCH => SearchStates::HAS_COUNTRY, // /country
            SearchStates::CITY_SEARCH => SearchStates::HAS_COUNTRY+SearchStates::HAS_CITY, // /country/city
            SearchStates::SPECIALIZATION_SEARCH => SearchStates::HAS_SPECIALIZATION,
            SearchStates::SUB_SPECIALIZATION_SEARCH => SearchStates::HAS_SPECIALIZATION + SearchStates::HAS_SUB_SPECIALIZATION, // /specialization/sub-specialization
            SearchStates::TREATMENT_SEARCH => SearchStates::HAS_SPECIALIZATION + SearchStates::HAS_TREATMENT,
            SearchStates::COUNTRY_SPECIALIZATION_SEARCH => SearchStates::HAS_COUNTRY + SearchStates::HAS_SPECIALIZATION,
            SearchStates::COUNTRY_SUB_SPECIALIZATION_SEARCH => SearchStates::HAS_COUNTRY + SearchStates::HAS_SPECIALIZATION + SearchStates::HAS_SUB_SPECIALIZATION,
            SearchStates::COUNTRY_TREATMENT_SEARCH => SearchStates::HAS_COUNTRY + SearchStates::HAS_SPECIALIZATION + SearchStates::HAS_TREATMENT,
            SearchStates::CITY_SPECIALIZATION_SEARCH => SearchStates::HAS_COUNTRY + SearchStates::HAS_CITY + SearchStates::HAS_SPECIALIZATION,
            SearchStates::CITY_SUB_SPECIALIZATION_SEARCH => SearchStates::HAS_COUNTRY + SearchStates::HAS_CITY + SearchStates::HAS_SPECIALIZATION + SearchStates::HAS_SUB_SPECIALIZATION,
            SearchStates::CITY_TREATMENT_SEARCH =>SearchStates::HAS_COUNTRY+ SearchStates::HAS_CITY + SearchStates::HAS_SPECIALIZATION + SearchStates::HAS_TREATMENT,
        );
    }
}

SearchStates::_initiateValue();