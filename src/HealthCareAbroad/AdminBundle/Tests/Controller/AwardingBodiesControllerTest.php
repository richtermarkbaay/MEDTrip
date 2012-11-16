<?php
/**
 * 
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class AwardingBodiesControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/awardingBodies');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Awarding Bodies")')->count(), 'No Output!');
    }
    
    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBodies/add');
    
    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Add Awarding Bodies")')->count(), '"Add Awarding Bodies" string not found!');
    }
    
    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBodies/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Awarding Bodies")')->count(), '"Edit Awarding Bodies" string not found!');
    }
    
    public function testAddSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBodies/add');

    	$formData = array(
    		'awardingBodies[name]' => 'Test awarding Bodies1',
    		'awardingBodies[details]' => 'Test',
    		'awardingBodies[website]' => 'test',
    		'awardingBodies[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    	
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	 
    	// check of redirect url /admin/awardingBodies
    	$this->assertEquals('/admin/awardingBodies', $client->getResponse()->headers->get('location'));
    	 
    	// redirect request
    	$crawler = $client->followRedirect(true);
    	
    	// check if the redirected response content has the newly added awardingBodies
    	$isAdded = $crawler->filter('#awardingBodies-list > tr > td:contains("'.$formData['awardingBodies[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }
    
    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBodies/edit/1');
    
    	$formData = array(
    			'awardingBodies[name]' => 'Test Awarding Bodies 1 Updated',
    			'awardingBodies[status]' => 1
    	);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    
    	// check of redirect url /admin/awardingBodies
    	$this->assertEquals('/admin/awardingBodies', $client->getResponse()->headers->get('location'));
    
    
    	// redirect request
    	$crawler = $client->followRedirect(true);
    
    	// check if the redirected response content has the newly added awardingBodies name
    	$isAdded = $crawler->filter('#awardingBodies-list > tr > td:contains("'.$formData['awardingBodies[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testUpdateStatusAction(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBodies/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBodies/add');
    
    	$formData = array(
    			'awardingBodies[name]' => '',
	    		'awardingBodies[details]' => '',
	    		'awardingBodies[website]' => '',
	    		'awardingBodies[status]' => 1
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
    		'awardingBodies[name]' => 'savesUsing GET',
    		'awardingBodies[details]' => 'Test',
    		'awardingBodies[website]' => 'test',
    		'awardingBodies[status]' => 1
    	);
    	$crawler = $client->request('GET', '/admin/awardingBodies/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
    
    public function testIndexWithFilters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        // Test Filter Active Status
        $crawler = $client->request('GET', '/admin/awardingBodies?status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#awardingBodies-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter status=1 is not working properly!');

        // Test Filter Inactive Status
        $crawler = $client->request('GET', '/admin/awardingBodies?status=0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllInactive = $crawler->filter('#awardingBodies-list tr a.icon-2')->count() == 0;
        $this->assertEquals(true, $isAllInactive, 'ListFilter status=0 is not working properly!');
    }
}