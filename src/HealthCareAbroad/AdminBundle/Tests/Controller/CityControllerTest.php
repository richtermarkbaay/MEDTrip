<?php
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class CityControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/cities');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Cities")')->count(), 'No Output!');
    }
    
    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/city/add');
    
    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Add City")')->count(), '"Add City" string not found!');
    }
    
    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/city/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit City")')->count(), '"Edit City" string not found!');
    }
    
    public function testAddSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/city/add');

    	$formData = array(
    		'city[name]' => 'TestCity1',
			'city[country]' => 1,
    		'city[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	 
    	// check of redirect url /admin/cities
    	$this->assertEquals('/admin/cities', $client->getResponse()->headers->get('location'));
    	 
    	 
    	// redirect request
    	$crawler = $client->followRedirect(true);

    	// check if the redirected response content has the newly added city
    	$isAdded = $crawler->filter('#city-list > tr > td:contains("'.$formData['city[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }
    
    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/city/edit/1');
    
    	$formData = array(
    			'city[name]' => 'TestCity1 Updated',
    			'city[country]' => 1,
    			'city[status]' => 1
    	);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    
    	// check of redirect url /admin/cities
    	$this->assertEquals('/admin/cities', $client->getResponse()->headers->get('location'));
    
    
    	// redirect request
    	$crawler = $client->followRedirect(true);
    
    	// check if the redirected response content has the newly added city name
    	$isAdded = $crawler->filter('#city-list > tr > td:contains("'.$formData['city[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testCreateDuplicate()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/city/add');
    
		$formData = array(
			'city[name]' => 'TestCity1 Updated',
			'city[country]' => 1,
			'city[status]' => 1
		);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 500
    	$hasDuplicateEntry = $client->getResponse()->getStatusCode() == 500;
    	$this->assertTrue($hasDuplicateEntry, '"City" must not be able to create an entry with duplicate name.');
    }

    public function testUpdateStatusAction(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/city/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/city/add');
    
    	$formData = array(
    			'city[name]' => '',
    			'city[country]' => 1,
    			'city[status]' => 1
    	);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    
    	$this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
    	$this->assertGreaterThan(0, $crawler->filter('form.basic-form > div ul')->count(), 'No validation message!');
    }
    
    public function testSaveInvalidMethod()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    
    	$formData = array(
    			'city[name]' => 'saveUsingGet',
    			'city[country]' => 1,
    			'city[status]' => 1
    	);
    	$crawler = $client->request('GET', '/admin/city/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
}