<?php
/**
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class GlobalAwardControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/global_award');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Awards, Certificates Or Affiliations")')->count(), 'No Output!');
    }
    
    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/global_award/add');
    
    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Add Award, Certificate Or Affiliation")')->count(), '"Add Award, Certificate Or Affiliation " string not found!');
    }
    
    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/global_award/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Award, Certificate Or Affiliation")')->count(), '"Edit Award, Certificate Or Affiliation " string not found!');
    }
    
    public function testAddSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/global_award/add');

    	$formData = array(
    		'global_award[name]' => 'TestGlobalAward1',
            'global_award[type]' => 2,
    		'global_award[details]' => 'test Details',
    		'global_award[awardingBody]' => 2,
			'global_award[country]' => 1,
    		'global_award[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	 
    	// check if redirect url /admin/global_award
    	$this->assertEquals('/admin/global_award', $client->getResponse()->headers->get('location'));
    	 
    	// redirect request
    	$crawler = $client->followRedirect(true);

    	// check if the redirected response content has the newly added global_award
    	$isAdded = $crawler->filter('#global_award-list > tr > td:contains("'.$formData['global_award[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }
    
    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/global_award/edit/2');
    
    	$formData = array(
    			'global_award[name]' => 'TestGlobalAward1 Updated',
                'global_award[type]' => 2,
    			'global_award[details]' => 'test Details',
    			'global_award[awardingBody]' => 1,
    			'global_award[country]' => 1,
    			'global_award[status]' => 1
    	);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    
    	// check of redirect url /admin/global_award
    	$this->assertEquals('/admin/global_award', $client->getResponse()->headers->get('location'));
    
    	// redirect request
    	$crawler = $client->followRedirect(true);
    
    	// check if the redirected response content has the newly added global_award name
    	$isAdded = $crawler->filter('#global_award-list > tr > td:contains("'.$formData['global_award[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testUpdateStatusAction(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/global_award/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/global_award/add');
    
    	$formData = array(
    			'global_award[name]' => '',
                'global_award[type]' => 2,
    			'global_award[details]' => '',
    			'global_award[awardingBody]' => 2,
    			'global_award[country]' => 1,
    			'global_award[status]' => 1
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
    			'global_award[name]' => 'saveUsingGet',
                'global_award[type]' => 2,
    			'global_award[details]' => 'test Details',
    			'global_award[awardingBody]' => 1,
    			'global_award[country]' => 1,
    			'global_award[status]' => 1
    	);
    	$crawler = $client->request('GET', '/admin/global_award/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
    
    public function testIndexWithFilters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        // Test Filter Active Status
        $crawler = $client->request('GET', '/admin/global_award?status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#global_award-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter is not working properly!');

        // Test Filter Inactive Status
        $crawler = $client->request('GET', '/admin/global_award?status=0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllInactive = $crawler->filter('#global_award-list tr a.icon-2')->count() == 0;
        $this->assertEquals(true, $isAllInactive, 'ListFilter is not working properly!');

        // Test Filter country and status
        $crawler = $client->request('GET', '/admin/global_award?country1=&status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $isFiltered = $crawler->filter('#global_award-list tr > td.country:not(:contains("Philippine"))')->count() == 0;
        $isAllActive = $crawler->filter('#global_award-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isFiltered && $isAllActive, 'Filter contry and status is not working properly!');
    }
    
    public function testCreateDuplicate()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/global_award/add');
    
        $formData = array(
                     		'global_award[name]' => 'test',
                            'global_award[type]' => 2,
                			'global_award[details]' => 'test',
                			'global_award[awardingBody]' => 1,
                			'global_award[country]' => 1,
                			'global_award[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);
    
        // check if status code is not 302
        $this->assertNotEquals(302, $client->getResponse()->getStatusCode(), '"Property value" must not be able to create an entry with duplicate name.');
    }
}