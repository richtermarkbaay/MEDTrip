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

class MedicalProcedureTypeControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/procedure-types');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Medical Procedure Types")')->count(), 'No Output!');
    }

    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/procedure-type/add');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Medical Procedure Type")')->count(), '"Add Medical Procedure Type" string not found!');
    }

    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/procedure-type/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Medical Procedure Type")')->count(), '"Edit Medical Procedure Type" string not found!');
    }

	public function testAddSave()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', '/admin/procedure-type/add');
    
		$formData = array(
			'medicalProcedureType[name]' => 'TestNewlyAdded MedProcType',
			'medicalProcedureType[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
			'medicalProcedureType[medical_center]' => 'AddedFromTest Center',
			'medicalProcedureType[status]' => 1
		);

		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $formData);

     	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
    	
    	// check of redirect url /admin/medical-procedure-types
    	$this->assertEquals('/admin/procedure-types', $client->getResponse()->headers->get('location'));

    	// redirect request
		$crawler = $client->followRedirect(true);
		
		// check if the redirected response content has the newly added procedure name
		$isAdded = $crawler->filter('#procedure-type-list > tr > td:contains("'.$formData['medicalProcedureType[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testEditSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/procedure-type/edit/2');

		$formData = array(
			'medicalProcedureType[name]' => 'TestNewlyAdded MedProcType Updated',
			'medicalProcedureType[description]' => 'the quick brown fox jump over the lazy dog! or Lorem ipsum dolor sit amit!',
			'medicalProcedureType[medical_center]' => 'AddedFromTest Center',
			'medicalProcedureType[status]' => 1
		);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());

    	// check of redirect url /admin/procedure-types
    	$this->assertEquals('/admin/procedure-types', $client->getResponse()->headers->get('location'));

    	// redirect request
		$crawler = $client->followRedirect(true);

    	// check if the redirected response content has the newly added procedure type name
    	$isAdded = $crawler->filter('#procedure-type-list > tr > td:contains("'.$formData['medicalProcedureType[name]'].'")')->count() > 0;
    	$this->assertTrue($isAdded);
    }

    public function testUpdateStatus(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/procedure-type/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
}


