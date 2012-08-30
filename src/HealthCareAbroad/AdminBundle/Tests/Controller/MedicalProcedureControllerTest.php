<?php
/**
 * Functional test for Admin MedicalProceduController
 *
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class MedicalProcedureControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/medical-procedures');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Medical Procedures")')->count(), 'No Output!');
    }

    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-procedure/add');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Medical Procedure")')->count(), '"Add Medical Procedure" string not found!');
    }

    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-procedure/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Medical Procedure")')->count(), '"Edit Medical Procedures" string not found!');
    }

    public function testAddSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-procedure/add');
    
    	$formData = array(
    		'medicalProcedure[name]' => 'testeste new',
    		'medicalProcedure[description]' => 'MedicalProcedure Description added from test data.',
   			'medicalProcedure[medicalProcedureType]' => 2,
    		'medicalProcedure[status]' => 1
    	);

     	$form = $crawler->selectButton('submit')->form();
     	$crawler = $client->submit($form, $formData);

     	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	
    	// check of redirect url /admin/medical-procedures
    	$this->assertEquals('/admin/medical-procedures', $client->getResponse()->headers->get('location'));
    	
    	
    	// redirect request
		$crawler = $client->followRedirect(true);
		
		// check if the redirected response content has the newly added procedure name
		$isAdded = $crawler->filter('#medical-procedure-list > tr > td:contains("'.$formData['medicalProcedure[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-procedure/edit/1');

    	$formData = array(
    			'medicalProcedure[name]' => 'testeste new updated',
    			'medicalProcedure[description]' => 'MedicalProcedure Description added from test data. Updated Description',
    			'medicalProcedure[medicalProcedureType]' => 2,
    			'medicalProcedure[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	 
    	// check of redirect url /admin/medical-procedures
    	$this->assertEquals('/admin/medical-procedures', $client->getResponse()->headers->get('location'));
    	 
    	 
    	// redirect request
    	$crawler = $client->followRedirect(true);
    
    	// check if the redirected response content has the newly added procedure name
    	$isAdded = $crawler->filter('#medical-procedure-list > tr > td:contains("'.$formData['medicalProcedure[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testCreateDuplicate()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-procedure/add');
    
    	$formData = array(
			'medicalProcedure[name]' => 'testeste new updated',
			'medicalProcedure[medicalProcedureType]' => 2,
    		'medicalProcedure[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 500
    	$hasDuplicateEntry = $client->getResponse()->getStatusCode() == 500;
    	$this->assertTrue($hasDuplicateEntry, '"Medical Procedure" must not be able to create an entry with duplicate ("medical_procedure_type_id", "name") fields.');
    }
    
    public function testUpdateStatusAction(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-procedure/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
   

    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-procedure/add');

    	$formData = array(
			'medicalProcedure[name]' => '',
			'medicalProcedure[medicalProcedureType]' => 2,
			'medicalProcedure[status]' => 1
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
			'medicalProcedure[name]' => 'saveUsingGet',
			'medicalProcedure[medicalProcedureType]' => 2,
			'medicalProcedure[status]' => 1
    	);
    	$crawler = $client->request('GET', '/admin/medical-procedure/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
}


