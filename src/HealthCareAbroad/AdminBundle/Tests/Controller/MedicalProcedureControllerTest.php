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
   			'medicalProcedure[medicalProcedureType]' => 2,
    		'medicalProcedure[status]' => 1
    	);

     	$form = $crawler->selectButton('submit')->first()->form();
     	$crawler = $client->submit($form, $formData);

     	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());

    	// check of redirect url /admin/medical-procedure/edit/{id}
    	$this->assertEquals('/admin/medical-procedure/edit/5', $client->getResponse()->headers->get('location'));

    	// redirect request
		$crawler = $client->followRedirect(true);
		
		// check if the redirected response content has the newly added procedure name
		$isAdded = $isAdded = $crawler->filter('#page-heading > h2:contains("Edit Medical Procedure")')->count() > 0;
    	$this->assertTrue($isAdded);
    }
    
    public function testAddSaveAnddAddAnother()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/medical-procedure/add');
    
        $formData = array(
            'medicalProcedure[name]' => 'testeste new withAddAnother',
            'medicalProcedure[medicalProcedureType]' => 2,
            'medicalProcedure[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->last()->form();
        $crawler = $client->submit($form, $formData);
    
        // check if redirect code 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
         
        // check of redirect url /admin/medical-procedure/add
        $this->assertEquals('/admin/medical-procedure/add', $client->getResponse()->headers->get('location'));
         
         
        // redirect request
        $crawler = $client->followRedirect(true);
    
        // check if the redirected response content has the newly added procedure name
		$isAdded = $isAdded = $crawler->filter('#page-heading > h2:contains("Add Medical Procedure")')->count() > 0;
        $this->assertTrue($isAdded);
    }

    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/medical-procedure/edit/1');

    	$formData = array(
    			'medicalProcedure[name]' => 'testeste new updated',
    			'medicalProcedure[medicalProcedureType]' => 2,
    			'medicalProcedure[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->first()->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());

    	// check of redirect url /admin/medical-procedure/edit/{id}
    	$this->assertEquals('/admin/medical-procedure/edit/1', $client->getResponse()->headers->get('location'));

    	// redirect request
    	$crawler = $client->followRedirect(true);

    	// check if the redirected response content has the newly added procedure name
        $isAdded = $isAdded = $crawler->filter('#page-heading > h2:contains("Edit Medical Procedure")')->count() > 0;
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

    	// check if status code is not 302
    	$this->assertNotEquals(302, $client->getResponse()->getStatusCode(), '"Medical Procedure" must not be able to create an entry with duplicate ("medical_procedure_type_id", "name") fields.');
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

    public function testIndexWithFilters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        // Test Filter Active Status
        $crawler = $client->request('GET', '/admin/medical-procedures?status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#medical-procedure-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter is not working properly!');

        // Test Filter Inactive Status
        $crawler = $client->request('GET', '/admin/medical-procedures?status=0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllInactive = $crawler->filter('#medical-procedure-list tr a.icon-2')->count() == 0;
        $this->assertEquals(true, $isAllInactive, 'ListFilter is not working properly!');
    
        // Test Filter medicalProcedureType
        $crawler = $client->request('GET', '/admin/medical-procedures?medicalProcedureType=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isFiltered = $crawler->filter('#medical-procedure-list tr > td.procedure-type:not(:contains("Procedure Type1"))')->count() == 0;
        $this->assertEquals(true, $isFiltered, 'Filter procedureType is not working properly!');

        // Test Filter status And medicalProcedureType
        $crawler = $client->request('GET', '/admin/medical-procedures?medicalProcedureType=2&status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isFiltered = $crawler->filter('#medical-procedure-list tr > td.procedure-type:not(:contains("Test Proc Type with center2"))')->count() == 0;
        $isAllActive = $crawler->filter('#medical-procedure-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isFiltered && $isAllActive, 'Filter procedureType and status is not working properly!');
    }
}


