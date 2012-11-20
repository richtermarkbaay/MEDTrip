<?php
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class CountryControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/countries');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Countries")')->count(), 'No Output!');
    }
    
    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/country/add');
    
    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Add Country")')->count(), '"Add Country" string not found!');
    }
    
    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/country/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Country")')->count(), '"Edit Country" string not found!');
    }
    
    public function testAddSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/country/add');

    	$formData = array(
    		'country[name]' => 'TestCountry1',
    		'country[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    	
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	 
    	// check of redirect url /admin/countries
    	$this->assertEquals('/admin/countries', $client->getResponse()->headers->get('location'));
    	 
    	 
    	// redirect request
    	$crawler = $client->followRedirect(true);
    	
    	// check if the redirected response content has the newly added country
    	$isAdded = $crawler->filter('#country-list > tr > td:contains("'.$formData['country[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }
    
    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/country/edit/1');
    
    	$formData = array(
    			'country[name]' => 'TestCountry1 Updated',
    			'country[status]' => 1
    	);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    
    	// check of redirect url /admin/countries
    	$this->assertEquals('/admin/countries', $client->getResponse()->headers->get('location'));
    
    
    	// redirect request
    	$crawler = $client->followRedirect(true);
    
    	// check if the redirected response content has the newly added country name
    	$isAdded = $crawler->filter('#country-list > tr > td:contains("'.$formData['country[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testCreateDuplicate()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/country/add');
    
		$formData = array(
			'country[name]' => 'TestCountry1 Updated',
			'country[status]' => 1
		);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if status code is not 302
    	$this->assertNotEquals(302, $client->getResponse()->getStatusCode(), '"Country" must not be able to create an entry with duplicate name.');
    }

    public function testUpdateStatusAction(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/country/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/country/add');
    
    	$formData = array(
    			'country[name]' => '',
    			'country[status]' => 1
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
    			'country[name]' => 'saveUsingGet',
    			'country[status]' => 1
    	);
    	$crawler = $client->request('GET', '/admin/country/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
    
    public function testIndexWithFilters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        // Test Filter Active Status
        $crawler = $client->request('GET', '/admin/countries?status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#country-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter status=1 is not working properly!');

        // Test Filter Inactive Status
        $crawler = $client->request('GET', '/admin/countries?status=0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllInactive = $crawler->filter('#country-list tr a.icon-2')->count() == 0;
        $this->assertEquals(true, $isAllInactive, 'ListFilter status=0 is not working properly!');
    }
}