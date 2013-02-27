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

class SubSpecializationControllerTest extends AdminBundleWebTestCase
{
    
    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/sub-specialization/add');
    	
    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Sub Specialization")')->count(), '"Add Sub Specialization " string not found!');
    }
    
    public function testAddFromSpecialization()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/sub-specialization/add?specializationId=1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Sub Specialization")')->count(), '"Add Sub Specialization " string not found!');
    }
    
    public function testAddWithInvalidSpecialization()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/sub-specialization/add?specializationId=10010');
    
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/sub-specialization/edit/2');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Sub Specialization")')->count(), '"Edit Sub Specialization" string not found!');
    }
    
    public function testEditWithInvalidSubSpecialization()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/sub-specialization/edit/10010');
    
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

	public function testAddSave()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', '/admin/sub-specialization/add');
    
		$formData = array(
			'subspecialization[name]' => 'TestNewlyAdded MedProcType',
			'subspecialization[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
			'subspecialization[specialization]' => 1,
			'subspecialization[status]' => 1
		);

		$form = $crawler->selectButton('submit')->first()->form();
		$crawler = $client->submit($form, $formData);

     	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());

    	// check of redirect url /admin/sub-specializations
    	$this->assertEquals('/admin/sub-specialization/edit/4', $client->getResponse()->headers->get('location'));

    	// redirect request
		$crawler = $client->followRedirect(true);

		// check if the redirected response content has the newly added procedure name
		$isAdded = $crawler->filter('h4:contains("Edit Sub Specialization")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testAddSaveAndAddAnother()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/sub-specialization/add');
    
        $formData = array(
            'subspecialization[name]' => 'TestNewlyAdded MedProcType with andAddAnother',
            'subspecialization[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
            'subspecialization[specialization]' => 1,
            'subspecialization[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->last()->form();
        $crawler = $client->submit($form, $formData);
    
        // check if redirect code 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    
        // check of redirect url /admin/sub-specializations
        $this->assertEquals('/admin/sub-specialization/add', $client->getResponse()->headers->get('location'));
    
        // redirect request
        $crawler = $client->followRedirect(true);
    
        // check if the redirected response content has the newly added procedure name
        $isAdded = $crawler->filter('h4:contains("Add Sub Specialization")')->count() > 0;
        $this->assertTrue($isAdded);
    }

    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/sub-specialization/edit/3');
    	
		$formData = array(
			'subspecialization[name]' => 'TestNewlyAdded MedProcType Updated',
			'subspecialization[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
			'subspecialization[specialization]' => 1,
			'subspecialization[status]' => 1
		);
        
    	$form = $crawler->selectButton('submit')->first()->form();
    	
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());

    	// check of redirect url /admin/procedure-types
    	$this->assertEquals('/admin/sub-specialization/edit/3', $client->getResponse()->headers->get('location'));

    	// redirect request
		$crawler = $client->followRedirect(true);

    	// check if the redirected response content has the newly added procedure type name
    	$isAdded = $isAdded = $crawler->filter('h4:contains("Edit Sub Specialization")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testEditSaveInvalidSubSpecialization()
    {
        $client = $this->getBrowserWithActualLoggedInUser();

        $formData = array(
            'subspecialization[name]' => 'TestNewlyAdded MedProcType Updated',
            'subspecialization[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
            'subspecialization[specialization]' => 1,
            'subspecialization[status]' => 1
        );
        
        $crawler = $client->request('POST', '/admin/sub-specialization/edit/10010', $formData);
    
        // check if redirect code 302
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testCreateDuplicate()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/sub-specialization/add');
    
		$formData = array(
			'subspecialization[name]' => 'TestNewlyAdded MedProcType Updated',
			'subspecialization[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
			'subspecialization[specialization]' => 1,
			'subspecialization[status]' => 1
		);
    
    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if status code is not 302
    	$this->assertNotEquals(302, $client->getResponse()->getStatusCode(), '"Procedure Type" must not be able to create an entry with duplicate ("medical_center_id", "name") fields.');
    }

    public function testUpdateStatus(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/sub-specialization/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/sub-specialization/add');
    
		$formData = array(
			'subspecialization[name]' => '',
			'subspecialization[description]' => 'the description',
			'subspecialization[specialization]' => 1,
			'subspecialization[status]' => 1
		);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	$this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
    	$this->assertGreaterThan(0, $crawler->filter('form.basic-form > div ul')->count(), 'No validation message!');
    }
    
    public function testEditSaveInvalidData()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/sub-specialization/edit/1');
    
        $formData = array(
                        'subspecialization[name]' => '',
                        'subspecialization[description]' => 'the description',
                        'subspecialization[specialization]' => 1,
                        'subspecialization[status]' => 1
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
			'subspecialization[name]' => '',
			'subspecialization[description]' => 'the description',
			'subspecialization[specialization]' => 1,
			'subspecialization[status]' => 1
    	);

    	$crawler = $client->request('GET', '/admin/sub-specialization/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
    
    public function testIndex()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/sub-specializations');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Sub Specialization")')->count(), 'Wrong page header');
    }
    
    public function testIndexWithFilters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        
        // Test Filter Active Status
        $crawler = $client->request('GET', '/admin/sub-specializations?status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#procedure-type-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter is not working properly!');

        // Test Filter Inactive Status
        $crawler = $client->request('GET', '/admin/sub-specializations?status=0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#procedure-type-list tr a.icon-2')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter is not working properly!');

        // Test Filter specialization
        $crawler = $client->request('GET', '/admin/sub-specializations?specialization=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isFiltered = $crawler->filter('#procedure-type-list tr > td > a.medical-center:not(:contains("Specialization 1"))')->count() == 0;
        $this->assertEquals(true, $isFiltered, 'Filter Specialization is not working properly!');

        // Test Filter status And specialization
        $crawler = $client->request('GET', '/admin/sub-specializations?specialization=1&status=1');
        $isFiltered = $crawler->filter('#procedure-type-list tr > td > a.medical-center:not(:contains("Specialization 1"))')->count() == 0;
        $isAllActive = $crawler->filter('#procedure-type-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isFiltered && $isAllActive, 'Filter Specialization and status is not working properly!');   
    }
}


