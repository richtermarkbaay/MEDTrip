<?php

namespace HealthCareAbroad\SearchBundle\Services;

/**
 * This class is only used in generating the search results url from search processess.
 * TODO: Improve this
 * 
 * @author Allejo Chris G. Velarde
 *
 */
use HealthCareAbroad\SearchBundle\Exception\SearchUrlGeneratorException;

class SearchUrlGenerator
{
    //TODO: consider using the constants in Search parameter service: 
    // Problem with that is we want to make the search url route parameters as independent as possible from the form parameters? 
    // i.e. countryId as key with a slug value doesn't sound right ??
     
    const SEARCH_URL_PARAMETER_COUNTRY = 'country';
    
    const SEARCH_URL_PARAMETER_CITY = 'city';
    
    const SEARCH_URL_PARAMETER_SPECIALIZATION = 'specialization';
    
    const SEARCH_URL_PARAMETER_SUB_SPECIALIZATION = 'sub_specialization';
    
    const SEARCH_URL_PARAMETER_TREATMENT = 'treatment';
    
    private $urlMapping = array();
    
    private $parameters = array();
    
    public function __construct()
    {
        $this->_initializeUrlMapping();
    }
    
    private function _initializeUrlMapping()
    {
        
        //TODO: find a way that this will dynamically set with the declared routes in routing.yml. only for this declared routes
        $this->urlMapping = array(
            // single search routes - these are declared in routing.yml
            SearchUrlRoutes::RESULTS_SPECIALIZATION => '/treatment/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION.'}',
            SearchUrlRoutes::RESULTS_SUB_SPECIALIZATION => '/treatment/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION.'}',
            SearchUrlRoutes::RESULTS_TREATMENT => '/treatment/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT.'}/treatment',
            SearchUrlRoutes::RESULTS_COUNTRY => '/destination/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY.'}',
            SearchUrlRoutes::RESULTS_CITY => '/destination/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY.'}',

            // combination routes
            SearchUrlRoutes::RESULTS_COUNTRY_SPECIALIZATION => '/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION.'}',
            SearchUrlRoutes::RESULTS_COUNTRY_SUB_SPECIALIZATION => '/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION.'}',
            SearchUrlRoutes::RESULTS_COUNTRY_TREATMENT => '/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT.'}/treatment',
            SearchUrlRoutes::RESULTS_CITY_SPECIALIZATION => '/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION.'}',
            SearchUrlRoutes::RESULTS_CITY_SUB_SPECIALIZATION => '/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION.'}',
            SearchUrlRoutes::RESULTS_CITY_TREATMENT => '/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION.'}/{'.SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT.'}/treatment',
        );
    }
    
    public function addParameter($parameterKey, $value)
    {
        $this->parameters[$parameterKey] = $value;
    }
    
    /**
     * Generate the url using the route name.
     * This only do simple variable replacements, no other tricks whatsoever 
     * 
     * @param unknown_type $routeName
     */
    public function generateByRouteName($routeName, $isDebug=false)
    {
        if ('' == \trim($routeName)) {
            throw SearchUrlGeneratorException::requiredRouteName();
        }
        
        if (!\array_key_exists($routeName, $this->urlMapping)) {
            throw SearchUrlGeneratorException::unknownRoute($routeName);
        }
        $url = $this->urlMapping[$routeName];
        
        // get the variables
        \preg_match_all('/\{.*?\}/', $this->urlMapping[$routeName], $matches);
        foreach ($matches[0] as $_matched_pattern) {
            $variable = \preg_replace('/[\{\}]/', '', $_matched_pattern);
            if (!isset($this->parameters[$variable])) {
                throw SearchUrlGeneratorException::missingMandatoryVariable($variable, $routeName);
            }
            
            // replace the value for the variable
            $url = \preg_replace('/'.$_matched_pattern.'/', $this->parameters[$variable], $url);
        }
        
        if ($isDebug) {
            $url = '/app_dev.php'.$url;
        }
        
        return $url;
    }
    
    static public function getSearchParameterKeyToSearchUrlKeyMapping()
    {
        return array(
            SearchParameterService::PARAMETER_KEY_COUNTRY_ID => SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY,
            SearchParameterService::PARAMETER_KEY_CITY_ID => SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY,
            SearchParameterService::PARAMETER_KEY_SPECIALIZATION_ID => SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION,
            SearchParameterService::PARAMETER_KEY_SUB_SPECIALIZATION_ID => SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION,
            SearchParameterService::PARAMETER_KEY_TREATMENT_ID => SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT,
        );
    }
}