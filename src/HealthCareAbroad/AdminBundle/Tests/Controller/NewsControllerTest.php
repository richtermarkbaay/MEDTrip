<?php
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class NewsControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/news');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of News")')->count(), 'No Output!');
    }
    
    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/news/add');
    
    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Add News")')->count(), '"Add News" string not found!');
    }
    
    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/news/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit News")')->count(), '"Edit News" string not found!');
    }
    
    public function testAddSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/News/add');

    	$formData = array(
    		'News[title]' => 'TestNews1',
			'News[description]' => 'test',
    		'News[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	 
    	// check of redirect url /admin/cities
    	$this->assertEquals('/admin/news', $client->getResponse()->headers->get('location'));
    	 
    	 
    	// redirect request
    	$crawler = $client->followRedirect(true);

    	// check if the redirected response content has the newly added News
    	$isAdded = $crawler->filter('#News-list > tr > td:contains("'.$formData['News[title]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }
    
    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/News/edit/1');
    
    	$formData = array(
    			'News[title]' => 'TestNews1 Updated',
    			'News[description]' => 'test',
    			'News[status]' => 1
    	);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    
    	// check of redirect url /admin/cities
    	$this->assertEquals('/admin/news', $client->getResponse()->headers->get('location'));
    
    
    	// redirect request
    	$crawler = $client->followRedirect(true);
    
    	// check if the redirected response content has the newly added News name
    	$isAdded = $crawler->filter('#News-list > tr > td:contains("'.$formData['News[title]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testCreateDuplicate()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/News/add');
    
		$formData = array(
			'News[title]' => 'TestNews1 Updated',
			'News[description]' => 'test',
			'News[status]' => 1
		);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if status code is not 302
    	$this->assertNotEquals(302, $client->getResponse()->getStatusCode(), '"News" must not be able to create an entry with duplicate name.');
    }

    public function testUpdateStatusAction(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/News/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/News/add');
    
    	$formData = array(
    			'News[title]' => '',
    			'News[description]' => 'test',
    			'News[status]' => 1
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
    			'News[title]' => 'saveUsingGet',
    			'News[description]' => 'test',
    			'News[status]' => 1
    	);
    	$crawler = $client->request('GET', '/admin/News/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
}