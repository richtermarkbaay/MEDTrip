<?php
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class MedicalCenterControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/medical-centers');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Center Types")')->count(), 'No Output!');
    }
    
    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-center/add');
    
    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Add Medical Center")')->count(), '"Add Medical Center" string not found!');
    }
    
    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-center/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Medical Center")')->count(), '"Edit Medical Center" string not found!');
    }
    
    public function testAddSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-center/add');

    	$formData = array(
    		'medicalCenter[name]' => 'addedby Institution fromtest',
    		'medicalCenter[description]' => 'The quick brown fox added from test.',
    		'medicalCenter[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	 
    	// check of redirect url /admin/medical-centers
    	$this->assertEquals('/admin/medical-centers', $client->getResponse()->headers->get('location'));
    	 
    	 
    	// redirect request
    	$crawler = $client->followRedirect(true);

    	// check if the redirected response content has the newly added medical center
    	$isAdded = $crawler->filter('#medical-center-list > tr > td:contains("'.$formData['medicalCenter[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }
    
    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-center/edit/1');
    
    	$formData = array(
    			'medicalCenter[name]' => 'addedby Institution fromtest updated',
    			'medicalCenter[description]' => 'The quick brown fox added from test. Updated from test.',
    			'medicalCenter[status]' => 1
    	);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);
    
    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    
    	// check of redirect url /admin/medical-centers
    	$this->assertEquals('/admin/medical-centers', $client->getResponse()->headers->get('location'));
    
    
    	// redirect request
    	$crawler = $client->followRedirect(true);
    
    	// check if the redirected response content has the newly added medical center name
    	$isAdded = $crawler->filter('#medical-center-list > tr > td:contains("'.$formData['medicalCenter[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testCreateDuplicate()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-center/add');
    
		$formData = array(
			'medicalCenter[name]' => 'addedby Institution fromtest',
			'medicalCenter[description]' => 'The quick brown fox added from test.',
			'medicalCenter[status]' => 1
		);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if status code is not 302
    	$this->assertNotEquals(302, $client->getResponse()->getStatusCode(), '"Medical Center" must not be able to create an entry with duplicate name.');
    }

    public function testUpdateStatusAction(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-center/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-center/add');
    
    	$formData = array(
    			'medicalCenter[name]' => '',
    			'medicalCenter[description]' => 'test invalid medical center data.',
    			'medicalCenter[status]' => 1
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
    			'medicalCenter[name]' => 'saveUsingGet',
    			'medicalCenter[description]' => 'test invalid medical center method.',
    			'medicalCenter[status]' => 1
    	);
    	$crawler = $client->request('GET', '/admin/medical-center/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
}