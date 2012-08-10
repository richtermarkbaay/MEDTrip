<?php

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class DefaultControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
        $uri ='/admin';
        
        // test that this should not be acessed by non-authenticated users
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->get('location')=='/admin/location' || $client->getResponse()->headers->get('location') == 'http://localhost/admin/login', 'Expecting redirect to login page and not to '.$client->getResponse()->headers->get('location'));
        
        // test with valid user
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('h1:contains("HealthCareAbroad.com Admin Panel")')->count(), 'Page heading should contatin "HealthCareAbroad.com Admin Panel"');
    }
    
    public function testSettings()
    {
        $uri = '/admin/settings';
        
        // test that this should not be acessed by non-authenticated users
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->get('location')=='/admin/location' || $client->getResponse()->headers->get('location') == 'http://localhost/admin/login', 'Expecting redirect to login page and not to '.$client->getResponse()->headers->get('location'));
        
        // test with valid user
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('h1:contains("HealthCareAbroad.com Admin Settings")')->count(), 'Page heading should contatin "HealthCareAbroad.com Admin Settings"');
        
        // test that this must not be accessed with a user with invalid roles
        $client = $this->getBrowserWithMockLoggedUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "Access is forbidden to not allowed roles");
    }
}
