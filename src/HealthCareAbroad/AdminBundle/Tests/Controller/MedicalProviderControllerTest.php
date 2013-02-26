<?php
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class MedicalProviderControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/medical-provider-groups');
        
        // test that we are in the correct page
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Medical Providers Group")')->count());
        
        // test that this must not be accessed with a user with invalid roles
        $client = $this->getBrowserWithMockLoggedUser();
        $crawler = $client->request('GET', '/admin/medical-provider-groups');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }
    
    public function testAdd()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/medical-provider-group/add');
    
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count(), '"Name" string not found!');
    }
   
    public function testEdit()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/medical-provider-group/edit/1');
    
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count(), '"Name" string not found!');
    }
    
    public function testSave()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/medical-provider-group/add');
    
        $formData = array(
                        'medical_provider_group[name]' => 'Test',
                        'medical_provider_group[description]' => 'test description',
                        'medical_provider_group[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);
    
    }
    
    public function testEditSave()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/medical-provider-group/edit/18');
    
        $formData = array(
                        'medical_provider_group[name]' => 'Test',
                        'medical_provider_group[description]' => 'test description',
                        'medical_provider_group[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);
    
        // check if redirect code 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    
        // check of redirect url /admin/language
        $this->assertEquals('/admin/medical-provider-groups', $client->getResponse()->headers->get('location'));
    
    
        // redirect request
        $crawler = $client->followRedirect(true);
    
        // check if the redirected response content has the newly added language name
        $isAdded = $crawler->filter('html:contains("Name")')->count() > 0;
        $this->assertTrue($isAdded);
    }
    
    public function testUpdateStatusAction(){
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/medical-provider-group/update-status/18');
    
        $response = $client->getResponse();
        $this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }  

    public function testSaveInvalidData()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/medical-provider-group/add');
    
        $formData = array(
                        'medical_provider_group[name]' => ' ',
                        'medical_provider_group[description]' => '',
                        'medical_provider_group[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);
    
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count(), 'No validation message!');
    }
    
    public function testSaveInvalidMethod()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        $formData = array(
                        'medical_provider_group[name]' => 'saveUsingGet',
                        'medical_provider_group[description]' => 'test',
                        'medical_provider_group[status]' => 1
        );
        $crawler = $client->request('GET', '/admin/medical-provider-group/test-save', $formData);
        $this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
}