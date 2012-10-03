<?php
/**
 * Functional test for Admin MedicalProceduTypeController
 *
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TreatmentControllerTest extends AdminBundleWebTestCase
{
    
    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/procedure-type/add');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Medical Procedure Type")')->count(), '"Add Medical Procedure Type" string not found!');
    }
    
    public function testAddFromMedicalCenter()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/procedure-type/add?medicalCenterId=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Medical Procedure Type")')->count(), '"Add Medical Procedure Type" string not found!');
    }
    
    public function testAddWithInvalidMedicalCenter()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/procedure-type/add?medicalCenterId=10010');
    
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/procedure-type/edit/2');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Medical Procedure Type")')->count(), '"Edit Medical Procedure Type" string not found!');
    }
    
    public function testEditWithInvalidProcedureType()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/procedure-type/edit/10010');
    
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

	public function testAddSave()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', '/admin/procedure-type/add');
    
		$formData = array(
			'medicalProcedureType[name]' => 'TestNewlyAdded MedProcType',
			'medicalProcedureType[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
			'medicalProcedureType[medicalCenter]' => 1,
			'medicalProcedureType[status]' => 1
		);

		$form = $crawler->selectButton('submit')->first()->form();
		$crawler = $client->submit($form, $formData);

     	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());

    	// check of redirect url /admin/medical-procedure-types
    	$this->assertEquals('/admin/procedure-type/edit/4', $client->getResponse()->headers->get('location'));

    	// redirect request
		$crawler = $client->followRedirect(true);

		// check if the redirected response content has the newly added procedure name
		$isAdded = $crawler->filter('#page-heading > h2:contains("Edit Medical Procedure Type")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testAddSaveAndAddAnother()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/procedure-type/add');
    
        $formData = array(
            'medicalProcedureType[name]' => 'TestNewlyAdded MedProcType with andAddAnother',
            'medicalProcedureType[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
            'medicalProcedureType[medicalCenter]' => 1,
            'medicalProcedureType[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->last()->form();
        $crawler = $client->submit($form, $formData);
    
        // check if redirect code 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    
        // check of redirect url /admin/medical-procedure-types
        $this->assertEquals('/admin/procedure-type/add', $client->getResponse()->headers->get('location'));
    
        // redirect request
        $crawler = $client->followRedirect(true);
    
        // check if the redirected response content has the newly added procedure name
        $isAdded = $crawler->filter('#page-heading > h2:contains("Add Medical Procedure Type")')->count() > 0;
        $this->assertTrue($isAdded);
    }

    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/procedure-type/edit/3');

		$formData = array(
			'medicalProcedureType[name]' => 'TestNewlyAdded MedProcType Updated',
			'medicalProcedureType[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
			'medicalProcedureType[medicalCenter]' => 1,
			'medicalProcedureType[status]' => 1
		);

    	$form = $crawler->selectButton('submit')->first()->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());

    	// check of redirect url /admin/procedure-types
    	$this->assertEquals('/admin/procedure-type/edit/3', $client->getResponse()->headers->get('location'));

    	// redirect request
		$crawler = $client->followRedirect(true);

    	// check if the redirected response content has the newly added procedure type name
    	$isAdded = $isAdded = $crawler->filter('#page-heading > h2:contains("Edit Medical Procedure Type")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testEditSaveInvalidProcedureType()
    {
        $client = $this->getBrowserWithActualLoggedInUser();

        $formData = array(
            'medicalProcedureType[name]' => 'TestNewlyAdded MedProcType Updated',
            'medicalProcedureType[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
            'medicalProcedureType[medicalCenter]' => 1,
            'medicalProcedureType[status]' => 1
        );
        
        $crawler = $client->request('POST', '/admin/procedure-type/edit/10010', $formData);
    
        // check if redirect code 302
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testCreateDuplicate()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/procedure-type/add');
    
		$formData = array(
			'medicalProcedureType[name]' => 'TestNewlyAdded MedProcType Updated',
			'medicalProcedureType[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
			'medicalProcedureType[medicalCenter]' => 1,
			'medicalProcedureType[status]' => 1
		);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if status code is not 302
    	$this->assertNotEquals(302, $client->getResponse()->getStatusCode(), '"Procedure Type" must not be able to create an entry with duplicate ("medical_center_id", "name") fields.');
    }

    public function testUpdateStatus(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/procedure-type/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/procedure-type/add');
    
		$formData = array(
			'medicalProcedureType[name]' => '',
			'medicalProcedureType[description]' => 'the description',
			'medicalProcedureType[medicalCenter]' => 1,
			'medicalProcedureType[status]' => 1
		);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	$this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
    	$this->assertGreaterThan(0, $crawler->filter('form.basic-form > div ul')->count(), 'No validation message!');
    }
    
    public function testEditSaveInvalidData()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/procedure-type/edit/1');
    
        $formData = array(
                        'medicalProcedureType[name]' => '',
                        'medicalProcedureType[description]' => 'the description',
                        'medicalProcedureType[medicalCenter]' => 1,
                        'medicalProcedureType[status]' => 1
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
			'medicalProcedureType[name]' => '',
			'medicalProcedureType[description]' => 'the description',
			'medicalProcedureType[medicalCenter]' => 1,
			'medicalProcedureType[status]' => 1
    	);

    	$crawler = $client->request('GET', '/admin/procedure-type/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
    
    public function testIndex()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/procedure-types');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Medical Procedure Types")')->count(), 'No Output!');
    }
    
    public function testIndexWithFilters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        
        // Test Filter Active Status
        $crawler = $client->request('GET', '/admin/procedure-types?status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#procedure-type-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter is not working properly!');

        // Test Filter Inactive Status
        $crawler = $client->request('GET', '/admin/procedure-types?status=0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#procedure-type-list tr a.icon-2')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter is not working properly!');

        // Test Filter medicalCenter
        $crawler = $client->request('GET', '/admin/procedure-types?medicalCenter=1');        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isFiltered = $crawler->filter('#procedure-type-list tr > td > a.medical-center:not(:contains("AddedFromTest Center"))')->count() == 0;
        $this->assertEquals(true, $isFiltered, 'Filter MedicalCenter is not working properly!');

        // Test Filter status And medicalCenter
        $crawler = $client->request('GET', '/admin/procedure-types?medicalCenter=1&status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isFiltered = $crawler->filter('#procedure-type-list tr > td > a.medical-center:not(:contains("AddedFromTest Center"))')->count() == 0;
        $isAllActive = $crawler->filter('#procedure-type-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isFiltered && $isAllActive, 'Filter MedicalCenter and status is not working properly!');   
    }
}


