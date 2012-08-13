<?php
/**
 * Functional test for Admin InstitutionController
 *
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InstitutionControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institutions');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Institutions")')->count(), 'No Output!');
    }

    public function testUpdateStatus()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/institution/1/update-status');
    
    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }

    /////// Manage Institution Medical Centers Tests //
	public function testManageCenters()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', '/admin/institution/1/manage-centers');

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Manage Institution Medical Center")')->count(), 'No Output!');
	}

	public function testAddMedicalCenters()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$postParams = array( 'centers' =>array(2, 3));
		$crawler = $client->request('POST', '/admin/institution/1/manage-centers', $postParams);
		
		$this->assertEquals(200, $client->getResponse()->getStatusCode());	
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Manage Institution Medical Center")')->count(), 'No Output!');
	}
	
	public function testAddDuplicateMedicalCenters()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$postParams = array('centers' => array(3));
		$crawler = $client->request('POST', '/admin/institution/1/manage-centers', $postParams);

		$this->assertEquals(500, $client->getResponse()->getStatusCode());
		$duplicateNotCreated = \trim($crawler->filter('div.text_exception > h1')->text()) != "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry";
		$this->assertTrue($duplicateNotCreated, 'institution_medical_centers Duplicate Entry.');
	}
	/////// END of Manage Institution Medical Centers Tests //


	/////// Manage Institution Medical Procedure Types Tests //
	public function testManageProcedureTypes()
	{
		$heading = 'Manage Institution Procedure Types';

		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', '/admin/institution/1/manage-procedure-types');

		$this->assertEquals(200, $client->getResponse()->getStatusCode());		
		$this->assertGreaterThan(0, $crawler->filter('html:contains("'.$heading.'")')->count(), 'Cannot find "'.$heading.'" heading.');
	}
	
	public function testAddProcedureTypes()
	{
		$heading = 'Manage Institution Procedure Types';
		
		$client = $this->getBrowserWithActualLoggedInUser();
		$postParams = array('procedure_types' =>array(2));
		$crawler = $client->request('POST', '/admin/institution/1/manage-procedure-types/2', $postParams);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("'.$heading.'")')->count(), 'Cannot find "'.$heading.'" heading.');
	}
	
	public function testAddDuplicateProcedureTypes()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$postParams = array('procedure_types' =>array(1,3));
		$crawler = $client->request('POST', '/admin/institution/1/manage-procedure-types/2', $postParams);

		$this->assertEquals(500, $client->getResponse()->getStatusCode());
		$duplicateNotCreated = \trim($crawler->filter('div.text_exception > h1')->text()) != "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry";
		$this->assertTrue($duplicateNotCreated, 'institution_medical_procedure_types Duplicate Entry.');
	}
	/////// END of Manage Institution Medical Procedure Types Tests //



	/////// Manage Institution Medical Procedures Tests //
	public function testManageProcedures()
	{
		$heading = 'List of Institutions Medical Procedures';
	
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', '/admin/institution/1/manage-procedures');

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("'.$heading.'")')->count(), 'Cannot find "'.$heading.'" heading.');
	}

	public function testAddProcedureWithoutProcedure()
	{
		$heading = 'List of Institutions Medical Procedures';
		$client = $this->getBrowserWithActualLoggedInUser();
	
		$postParams['institutionMedicalProcedure'] = array(
			'description' => 'this is generated from testAddInactiveProcedure',
			'status' => 1
		);

		$crawler = $client->request('POST', '/admin/institution/1/save-medical-procedure', $postParams);

		$this->assertEquals(302, $client->getResponse()->getStatusCode());

		$expectedRedirectUrl = '/admin/institution/1/manage-procedures';
		$this->assertEquals($expectedRedirectUrl, substr($client->getResponse()->headers->get('location'), 0, strlen($expectedRedirectUrl)));
  
		// Redirect result
		$crawler = $client->followRedirect(true);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		
		$hasNotBeenAdded = "Medical Procedure does not exists or already inactive!" == \trim($crawler->filter('#content-table-inner > #message-red')->text());
		$this->assertTrue($hasNotBeenAdded);
	}

	public function testAddProcedure()
	{ 
		$heading = 'List of Institutions Medical Procedures';
		$client = $this->getBrowserWithActualLoggedInUser();

		$postParams['institutionMedicalProcedure'] = array(
			'medical_procedure' => 3,
			'description' => 'this is generated from test',
			'status' => 1
		);

		$crawler = $client->request('POST', '/admin/institution/1/save-medical-procedure', $postParams);


		$this->assertEquals(302, $client->getResponse()->getStatusCode());

		$expectedRedirectUrl = '/admin/institution/1/manage-procedures';
		$this->assertEquals($expectedRedirectUrl, substr($client->getResponse()->headers->get('location'), 0, strlen($expectedRedirectUrl)));

		// Redirect result eagerly wanted to learn deep regarding
		$crawler = $client->followRedirect(true);

		// Check if status is 200 and the newly added procedure does exists in the list.
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0, $crawler->filter('table#institution-procedure-list > tr > td:contains("testProcedure2")')->count(), 'Cannot find newly added procedure.');
	}

	public function testAddDuplicateProcedure()
	{
		$heading = 'List of Institutions Medical Procedures';
		$client = $this->getBrowserWithActualLoggedInUser();

		$postParams['institutionMedicalProcedure'] = array(
				'medical_procedure' => 3,
				'description' => 'this is generated from test',
				'status' => 1
		);

		$crawler = $client->request('POST', '/admin/institution/1/save-medical-procedure', $postParams);
		
		$this->assertEquals(500, $client->getResponse()->getStatusCode());

		$duplicateNotCreated = \trim($crawler->filter('div.text_exception > h1')->text()) != "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry";
		$this->assertTrue($duplicateNotCreated, "institution_medical_procedures Duplicate Entry.");
	}
	
	public function testUpdateProcedureStatus()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$params = array('institution_medical_procedure_id' => 1);
		$crawler = $client->request('GET', '/admin/institution/update-procedure-status', $params);

		$this->assertEquals("true", $client->getResponse()->getContent(), "Unable to update procedure status.");
		$this->assertEquals("Response code: 200", "Response code: " . $client->getResponse()->getStatusCode());
	}
	
	public function testLoadProcedureTypes()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$params = array('medical_center_id' => 1);
		$crawler = $client->request('GET', '/admin/medical-center/load-procedure-types', $params);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}
	
	public function testLoadProcedures()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$params = array('institution_id' => 1, 'procedure_type_id' => 1);
		$crawler = $client->request('GET', '/admin/procedure-type/load-procedures', $params);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}
}