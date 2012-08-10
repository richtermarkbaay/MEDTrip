<?php

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class AdminUserTypeControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/settings/user-types');
        
        // test that we are in the correct page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Admin user types")')->count());
        
        // test that this must not be accessed with a user with invalid roles
        $client = $this->getBrowserWithMockLoggedUser();
        $crawler = $client->request('GET', '/admin/settings/user-types');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
    
    public function testAdd()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/settings/user-types/add');
        
        // test that we are in the correct page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('h1:contains("Create new user type")')->count());
        
        // submit form
        $formValues = array('adminUserTypeForm[name]' => 'test only');
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        
        // after successful save, should be redirected to user types index
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals("/admin/settings/user-types", $client->getResponse()->headers->get('location'));
        
        // test invalid form
        $crawler = $client->request('GET', '/admin/settings/user-types/add');
        $formValues = array('adminUserTypeForm[name]' => '');
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("This value should not be blank.")')->count(), 'Text "This value should not be blank." not found after validating form');
        
        // test that this must not be accessed with a user with invalid roles
        $client = $this->getBrowserWithMockLoggedUser();
        $crawler = $client->request('GET', '/admin/settings/user-types/add');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
}