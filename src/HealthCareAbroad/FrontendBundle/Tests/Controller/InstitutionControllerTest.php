<?php

namespace HealthCareAbroad\FrontendBundle\Tests\Controller;

use HealthCareAbroad\FrontendBundle\Tests\FrontendBundleWebTestCase;


class InstitutionControllerTest extends FrontendBundleWebTestCase
{
    
    public function testProfile()
    {
        $uri = "/hospital/ahalia-eye-hospital";
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertGreaterThan(0,$crawler->filter('h3:contains("Hospital")')->count());
        $this->assertGreaterThan(0,$crawler->filter('span:contains("Audiology Services")')->count());
        $this->assertGreaterThan(0,$crawler->filter('h5:contains("Clinics")')->count());
        $this->assertGreaterThan(0,$crawler->filter('h5:contains("Services")')->count());
        $this->assertGreaterThan(0,$crawler->filter('h5:contains("Awards")')->count());
        
        $this->assertGreaterThan(0,$crawler->filter('img.featured-image')->count());
//         $this->assertGreaterThan(0,$crawler->filter('span:contains("hospital-default-logo")')->count());
        
        
        $uri = "/hospital/test-single-hospital";
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertGreaterThan(0,$crawler->filter('h3:contains("Hospital")')->count());
        
        
        //test invalid institutionSlug
        $uri = "/hospital/test-singlasde-hospital";
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(404,$client->getResponse()->getStatusCode());
    }
    
}