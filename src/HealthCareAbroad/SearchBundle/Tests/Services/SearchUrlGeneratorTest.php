<?php

namespace HealthCareAbroad\SearchBundle\Tests\Services;

use HealthCareAbroad\SearchBundle\Services\SearchUrlRoutes;

use HealthCareAbroad\SearchBundle\Services\SearchUrlGenerator;

use HealthCareAbroad\SearchBundle\Tests\ContainerAwareUnitTestCase;

class SearchUrlGeneratorTest extends ContainerAwareUnitTestCase
{
    public function testGenerateByRouteNameForResultSpecialization()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION, 's1');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_SPECIALIZATION);
        $expectedUrl = '/treatment/s1';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
    public function testGenerateByRouteNameForResultSubSpecialization()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION, 's1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION, 'sb1');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_SUB_SPECIALIZATION);
        $expectedUrl = '/treatment/s1/sb1';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
    public function testGenerateByRouteNameForResultTreatment()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION, 's1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT, 'tr1');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_TREATMENT);
        $expectedUrl = '/treatment/s1/tr1/treatment';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
    public function testGenerateByRouteNameForResultCountry()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY, 'c1');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_COUNTRY);
        $expectedUrl = '/destination/c1';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
    public function testGenerateByRouteNameForResultCity()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY, 'c1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY, 'ct1');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_CITY);
        $expectedUrl = '/destination/c1/ct1';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
    public function testGenerateByRouteNameForResultCountrySpecialization()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY, 'c1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION, 's1');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_COUNTRY_SPECIALIZATION);
        $expectedUrl = '/c1/s1';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
    public function testGenerateByRouteNameForResultCountrySubSpecialization()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY, 'c1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION, 's1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION, 'ssb');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_COUNTRY_SUB_SPECIALIZATION);
        $expectedUrl = '/c1/s1/ssb';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
    public function testGenerateByRouteNameForResultCountryTreatment()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY, 'c1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION, 's1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT, 'tr1');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_COUNTRY_TREATMENT);
        $expectedUrl = '/c1/s1/tr1/treatment';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
    public function testGenerateByRouteNameForResultCitySpecialization()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY, 'c1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY, 'ct1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION, 's1');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_CITY_SPECIALIZATION);
        $expectedUrl = '/c1/ct1/s1';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
    public function testGenerateByRouteNameForResultCitySubSpecialization()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY, 'c1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY, 'ct1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION, 's1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION, 'ssb');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_CITY_SUB_SPECIALIZATION);
        $expectedUrl = '/c1/ct1/s1/ssb';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
    public function testGenerateByRouteNameForResultCityTreatment()
    {
        $generator = new SearchUrlGenerator();
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY, 'c1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY, 'ct1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION, 's1');
        $generator->addParameter(SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT, 'tr1');
        $url = $generator->generateByRouteName(SearchUrlRoutes::RESULTS_CITY_TREATMENT);
        $expectedUrl = '/c1/ct1/s1/tr1/treatment';
        $this->assertEquals($expectedUrl, $url, "Failed asserting that expected url: {$expectedUrl} is equal to generated url: {$url} ");
    }
    
}