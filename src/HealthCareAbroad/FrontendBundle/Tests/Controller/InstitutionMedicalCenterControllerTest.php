<?php

namespace HealthCareAbroad\FrontendBundle\Tests\Controller;

use HealthCareAbroad\FrontendBundle\Tests\FrontendBundleWebTestCase;


class InstitutionMedicalCenterControllerTest extends FrontendBundleWebTestCase
{
    
    public function testProfile()
    {
        $uri = "/ahalia-eye-hospital/clinic/audiology-services";
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
//         var_dump($crawler->filter('html'));exit;
        $this->assertGreaterThan(0,$crawler->filter('h2:contains("Ahalia Eye Hospital")')->count());
        $this->assertGreaterThan(0,$crawler->filter('h2:contains("Audiology Services")')->count());
        $this->assertGreaterThan(0,$crawler->filter('span.clinic-default-logo')->count());
        $this->assertGreaterThan(0,$crawler->filter('span.hospital-default-logo')->count());
        $this->assertEquals(0,$crawler->filter('img.featured-image')->count());
        
        $this->assertGreaterThan(0,$crawler->filter('h5:contains("Specializations")')->count());
        $this->assertGreaterThan(0,$crawler->filter('a:contains("Allergy and Immunology")')->count());
        
        $this->assertGreaterThan(0,$crawler->filter('h5:contains("Doctors")')->count());
        $this->assertGreaterThan(0,$crawler->filter('strong:contains("Dr. Pankaj  Chaturvedi")')->count());
        $this->assertGreaterThan(0,$crawler->filter('p:contains("Allergy and Immunology")')->count());
        
        //test with invalid imc slug
        $uri = "/ahalia-eye-hospital/clinic/audiology-serviceses";
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(404,$client->getResponse()->getStatusCode());

        $uri = "/test-single-hospital/clinic/test-single";
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertGreaterThan(0,$crawler->filter('h2:contains("Test Single Hospital")')->count());
        $this->assertEquals(0,$crawler->filter('img.featured-image')->count());
    }
    
    
    
    
}