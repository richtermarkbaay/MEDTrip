<?php

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class DefaultControllerTest extends InstitutionBundleWebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/institution'); // request with no logged in user
        
        $this->assertEquals(302, $client->getResponse()->getStatusCode());// expecting a redirect to login form
        $redirectLocation = $client->getResponse()->headers->get('location');
        $this->assertTrue($redirectLocation=='/institution/location' || $redirectLocation == 'http://localhost/institution/login');
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/institution');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    
    public function testError403()
    {
        $uri = '/institution/access-denied';
        $client = static::createClient();
        
        // test access with not logged in user
        $client->request('GET', $uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());// expecting a redirect to login form
        $this->assertTrue($this->isRedirectedToLoginPage($client));
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("You do not have proper credentials to access this page")')->count(), 'Expecting text "You do not have proper credentials to access this page"');
    }
}
