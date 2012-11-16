<?php
/**
 * 
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class AffiliationControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/affiliation');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Affiliation")')->count(), 'No Output!');
    }
    
    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/affiliation/add');
    
    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Add Affiliation")')->count(), '"Add Affiliation" string not found!');
    }
    
    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/affiliation/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Affiliation")')->count(), '"Edit Affiliation" string not found!');
    }
    
    public function testAddSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/affiliation/add');

    	$formData = array(
    		'affiliation[name]' => 'TestAffiliation1',
    		'affiliation[details]' => 'test Details',
    		'affiliation[awardingBodies]' => 2,
			'affiliation[country]' => 1,
    		'affiliation[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	 
    	// check if redirect url /admin/affiliation
    	$this->assertEquals('/admin/affiliation', $client->getResponse()->headers->get('location'));
    	 
    	// redirect request
    	$crawler = $client->followRedirect(true);

    	// check if the redirected response content has the newly added affiliation
    	$isAdded = $crawler->filter('#affiliation-list > tr > td:contains("'.$formData['affiliation[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }
    
    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/affiliation/edit/2');
    
    	$formData = array(
    			'affiliation[name]' => 'TestAffiliation1 Updated',
    			'affiliation[details]' => 'test Details',
    			'affiliation[awardingBodies]' => 1,
    			'affiliation[country]' => 1,
    			'affiliation[status]' => 1
    	);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    
    	// check of redirect url /admin/affiliation
    	$this->assertEquals('/admin/affiliation', $client->getResponse()->headers->get('location'));
    
    	// redirect request
    	$crawler = $client->followRedirect(true);
    
    	// check if the redirected response content has the newly added affiliation name
    	$isAdded = $crawler->filter('#affiliation-list > tr > td:contains("'.$formData['affiliation[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testUpdateStatusAction(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/affiliation/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/affiliation/add');
    
    	$formData = array(
    			'affiliation[name]' => '',
    			'affiliation[details]' => '',
    			'affiliation[awardingBodies]' => 2,
    			'affiliation[country]' => 1,
    			'affiliation[status]' => 1
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
    			'affiliation[name]' => 'saveUsingGet',
    			'affiliation[details]' => 'test Details',
    			'affiliation[awardingBodies]' => 1,
    			'affiliation[country]' => 1,
    			'affiliation[status]' => 1
    	);
    	$crawler = $client->request('GET', '/admin/affiliation/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
    
    public function testIndexWithFilters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        // Test Filter Active Status
        $crawler = $client->request('GET', '/admin/affiliation?status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#affiliation-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter is not working properly!');

        // Test Filter Inactive Status
        $crawler = $client->request('GET', '/admin/affiliation?status=0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllInactive = $crawler->filter('#affiliation-list tr a.icon-2')->count() == 0;
        $this->assertEquals(true, $isAllInactive, 'ListFilter is not working properly!');

        // Test Filter country and status
        $crawler = $client->request('GET', '/admin/affiliation?country1=&status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $isFiltered = $crawler->filter('#affiliation-list tr > td.country:not(:contains("Philippine"))')->count() == 0;
        $isAllActive = $crawler->filter('#affiliation-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isFiltered && $isAllActive, 'Filter contry and status is not working properly!');
    }
}