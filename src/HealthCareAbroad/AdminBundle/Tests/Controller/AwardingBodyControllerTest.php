<?php
/**
 * Functional Test for AwardingBodyController
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class AwardingBodyControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/awardingBody');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Awarding Bodies")')->count(), 'No Output!');
    }
    
    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBody/add');
    
    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Add Awarding Body")')->count(), '"Add Awarding Body" string not found!');
    }
    
    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBody/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Awarding Body")')->count(), '"Edit Awarding Body" string not found!');
    }
    
    public function testAddSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBody/add');

    	$formData = array(
    		'awardingBody[name]' => 'Test awarding Body1',
    		'awardingBody[details]' => 'Test',
    		'awardingBody[website]' => 'test',
    		'awardingBody[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    	
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	 
    	// check of redirect url /admin/awardingBody
    	$this->assertEquals('/admin/awardingBody', $client->getResponse()->headers->get('location'));
    	 
    	// redirect request
    	$crawler = $client->followRedirect(true);
    	
    	// check if the redirected response content has the newly added awardingBody
    	$isAdded = $crawler->filter('#awardingBody-list > tr > td:contains("'.$formData['awardingBody[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }
    
    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBody/edit/1');
    
    	$formData = array(
    			'awardingBody[name]' => 'Test Awarding Body 1 Updated',
    			'awardingBody[status]' => 1
    	);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    
    	// check of redirect url /admin/awardingBody
    	$this->assertEquals('/admin/awardingBody', $client->getResponse()->headers->get('location'));
    
    
    	// redirect request
    	$crawler = $client->followRedirect(true);
    
    	// check if the redirected response content has the newly added awardingBody name
    	$isAdded = $crawler->filter('#awardingBody-list > tr > td:contains("'.$formData['awardingBody[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testUpdateStatusAction(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBody/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/awardingBody/add');
    
    	$formData = array(
    			'awardingBody[name]' => '',
	    		'awardingBody[details]' => '',
	    		'awardingBody[website]' => '',
	    		'awardingBody[status]' => 1
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
    		'awardingBody[name]' => 'savesUsing GET',
    		'awardingBody[details]' => 'Test',
    		'awardingBody[website]' => 'test',
    		'awardingBody[status]' => 1
    	);
    	$crawler = $client->request('GET', '/admin/awardingBody/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
    
    public function testIndexWithFilters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        // Test Filter Active Status
        $crawler = $client->request('GET', '/admin/awardingBody?status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#awardingBody-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter status=1 is not working properly!');

        // Test Filter Inactive Status
        $crawler = $client->request('GET', '/admin/awardingBody?status=0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllInactive = $crawler->filter('#awardingBody-list tr a.icon-2')->count() == 0;
        $this->assertEquals(true, $isAllInactive, 'ListFilter status=0 is not working properly!');
    }
}