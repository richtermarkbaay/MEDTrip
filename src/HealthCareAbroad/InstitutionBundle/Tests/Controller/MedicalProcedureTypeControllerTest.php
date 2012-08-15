<?php 

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class MedicalProcedureTypeControllerTest extends InstitutionBundleWebTestCase
{	
    
    public function testIndex()
    {
        $uri = '/institution/listings';
        $client = $this->requestUrlWithNoLoggedInUser($uri);
        
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $location = $this->getLocationResponseHeader($client);
        $this->assertTrue($this->loginAbsoluteUri == $location || $this->loginRelativeUri == $location );
        
        // test with valid user
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertGreaterThan(0, $crawler->filter('title:contains("Listing Management")')->count(), 'Expecting page title to contain "Listing Management"');
    }
    
    public function testAdd()
    {
        $uri = '/institution/listing/add';
        
        // test accessing with no user
        $client = $this->requestUrlWithNoLoggedInUser($uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $location = $this->getLocationResponseHeader($client);
        $this->assertTrue($this->loginAbsoluteUri == $location || $this->loginRelativeUri == $location );
        
        // test with valid user
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertGreaterThan(0, $crawler->filter('title:contains("Add Listing")')->count(), 'Expecting page title to contain "Add Listing"');
        
        // valid form values
        $formValues = array(
            'institutionMedicalProcedureTypeForm[medicalCenter]' => '1',
            'institutionMedicalProcedureTypeForm[medicalProcedureType]' => '1',
            'institutionMedicalProcedureTypeForm[description]' => 'Test listing',
        );
        
        $invalidFormValues = array(
            'institutionMedicalProcedureTypeForm[medicalCenter]' => '1',
            'institutionMedicalProcedureTypeForm[medicalProcedureType]' => '',
            'institutionMedicalProcedureTypeForm[description]' => '',
        );
        
        // test for submitting missing required fields
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("This value should not be blank.")')->count(), 'Text "This value should not be blank." not found after validating form');
        
        // test for submitting correct fields
        $crawler = $client->request('GET', $uri);
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), "Expecting redirect after submitting correct data");
        
        $client->followRedirect();
        
        $this->assertGreaterThan(0, $crawler->filter('title:contains("Edit Listing")')->count(), 'Expecting to be in "Edit Listing" page after saving new listing');
    }
    
	public function testLoadProcedures()
	{
	    $this->markTestIncomplete();
// 		$client = $this->getBrowserWithActualLoggedInUser();
// 		$params = array('institution_id' => 1, 'procedure_type_id' => 1);
// 		$crawler = $client->request('GET', '/institution/procedure-type/load-procedures', $params);

// 		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}
	
}