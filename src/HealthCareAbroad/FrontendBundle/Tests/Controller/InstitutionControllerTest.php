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
        $this->assertGreaterThan(0,$crawler->filter('img.featured-image')->count());
        $this->assertGreaterThan(0,$crawler->filter('img.hospital-logo')->count());
        
        //clinics section details
        $this->assertGreaterThan(0,$crawler->filter('h5:contains("Clinics")')->count());
        $this->assertGreaterThan(0,$crawler->filter('span:contains("Audiology Services")')->count());
        $this->assertGreaterThan(0,$crawler->filter('span:contains("Pre-Admission Counselling and Evaluation (PACE) Clinic")')->count());
        
        //services section details
        $this->assertGreaterThan(0,$crawler->filter('h5:contains("Services")')->count());
        $this->assertGreaterThan(0,$crawler->filter('li:contains("Text Message Reminders")')->count());
        
        //awards section details
        $this->assertGreaterThan(0,$crawler->filter('h5:contains("Awards")')->count());
        $this->assertGreaterThan(0,$crawler->filter('li:contains("test")')->count());
        
        //doctors section details
        $this->assertGreaterThan(0,$crawler->filter('a:contains("DOCTORS")')->count());
        $this->assertGreaterThan(0,$crawler->filter('span.doctor-icon')->count());
        $this->assertGreaterThan(0,$crawler->filter('strong:contains("Dr. Pankaj  Chaturvedi")')->count());
        $this->assertGreaterThan(0,$crawler->filter('strong:contains("Dr. test  test")')->count());
        $this->assertGreaterThan(0,$crawler->filter('p:contains("Allergy and Immunology")')->count());
        
        
        $uri = "/hospital/test-single-hospital";
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertGreaterThan(0,$crawler->filter('h3:contains("Test Single Hospital")')->count());
        $this->assertGreaterThan(0,$crawler->filter('address:contains("Apollo Gleneagles Hospital, No. 58, Canal Circular Road 700054")')->count());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("1 3323 203 040")')->count());
        $this->assertGreaterThan(0,$crawler->filter('span.clinic-default-logo')->count());
        
        
        //test invalid institutionSlug
        $uri = "/hospital/test-singlasde-hospital";
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(404,$client->getResponse()->getStatusCode());
    }
    
}