<?php 

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class TreatmentControllerTest extends InstitutionBundleWebTestCase
{	
    
    public function testPreExecute()
    {
        // test invalid imcId
        $uri = '/institution/medical-centers/edit/9999999999/medical-procedure-types/';
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('GET', $uri);
        
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testIndex()
    {
        $uri = '/institution/medical-centers/edit/1/medical-procedure-types/';
        
        // test access by no logged user
        $client = $this->requestUrlWithNoLoggedInUser($uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client));
        
        // test with valid user
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertGreaterThan(0, $crawler->filter('title:contains("Institution Panel Treatments")')->count(), 'Expecting page title to contain "Institution Panel Treatments"');
    }
    
    public function testAdd()
    {
        $uri = '/institution/medical-centers/edit/1/medical-procedure-types/add';
        
        // test accessing with no user
        $client = $this->requestUrlWithNoLoggedInUser($uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client), 'Expecting redirection to login page');
        
        // test with valid user
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        
        $this->assertGreaterThan(0, $crawler->filter('form :contains("Treatment")')->count(), 'Expecting field "Treatment"');
        
        // valid form values
        $formValues = array(
            //'institutionTreatmentForm[medicalCenter]' => '1',
            'institutionTreatmentForm[treatment]' => 2,
            'institutionTreatmentForm[description]' => 'Test medical-procedure-type',
        );
        
        $invalidFormValues = array(
            //'institutionTreatmentForm[medicalCenter]' => '1',
            'institutionTreatmentForm[treatment]' => 2,
            'institutionTreatmentForm[description]' => '',
        );
        
        // test for submitting missing required fields
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        //$crawler = $client->request('POST', $uri, $invalidFormValues, array('X-Requested-With' => 'XMLHttpRequest'));
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("This value should not be blank.")')->count(), 'Text "This value should not be blank." not found after validating form');
        
        // test for submitting correct fields
        $crawler = $client->request('GET', $uri);
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "");
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $content = \json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('/institution/medical-centers/edit/1', $content['redirect_url']);
    }
    
    /**
     * @depends testAdd
     */
    public function testEdit()
    {
        $uri = '/institution/medical-centers/edit/1/medical-procedure-types/edit/1';
        
        // test accessing with no user
        $client = $this->requestUrlWithNoLoggedInUser($uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client), 'Expecting redirection to login page');
        
        // test with valid user
        $client = $this->getBrowserWithActualLoggedInUser();
        
        // test with invalid imptId
        $client->request('GET','/institution/medical-centers/edit/1/medical-procedure-types/edit/9999999');
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'Expecting 404 error for invalid imptId');
        
        // test correct request
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Description")')->count(), 'Expecting Description form field');
        
        // valid form values
        $formValues = array(
            //'institutionTreatmentForm[medicalCenter]' => '1',
            'institutionTreatmentForm[treatment]' => 1,
            'institutionTreatmentForm[description]' => 'Test medical-procedure-type',
        );
        
        $invalidFormValues = array(
            //'institutionTreatmentForm[medicalCenter]' => '1',
            'institutionTreatmentForm[treatment]' => '1',
            'institutionTreatmentForm[description]' => '',
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
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $content = \json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('/institution/medical-centers/edit/1?imptId=1', $content['redirect_url']);
    }
    
    public function testSave()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
         
        // --- test not allowed methods
        $client->request('GET', '/institution/medical-centers/1/medical-procedure-types/testSave', array());
        
        $this->assertEquals(405, $client->getResponse()->getStatusCode(), "POST is the only allowed method");
         
        $client->request('GET', '/institution/medical-centers/1/medical-procedure-types/testSave');
        $this->assertEquals(405, $client->getResponse()->getStatusCode(), "POST is the only allowed method");
         
        $client->request('GET', '/institution/medical-centers/1/medical-procedure-types/testSave');
        $this->assertEquals(405, $client->getResponse()->getStatusCode(), "POST is the only allowed method");
         
        // -- test posting with invalid imcId
        $client->request('POST', '/institution/medical-centers/edit/1/medical-procedure-types/edit/999999999');
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "Expecting 404 if passed invalid imptId");
    }
	
    public function testAddMedicalProcedure()
    {
        $uri = '/institution/medical-procedure-types/1/add-procedure';
        
        // test accessing with no user
        $client = $this->requestUrlWithNoLoggedInUser($uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client), 'Expecting redirection to login page');
        
        $client = $this->getBrowserWithActualLoggedInUser();
        
        // test invalid imptId
        $client->request('GET', '/institution/medical-procedure-types/9999999999/add-procedure');
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'Expecting 404 for invalid imptId');
        
        // test valid request
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Specialization")')->count(), 'Specialization field is expected');
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Procedure Type")')->count(), 'Procedure Type field is expected');
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Procedure")')->count(), 'Procedure field is expected');
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Description")')->count(), 'Description field is expected');
        
        $form = $crawler->selectButton('submit')->form();
        $invalidValues = array('institutionMedicalProcedureForm[treatmentProcedure]' => 0,
            'institutionMedicalProcedureForm[description]' => '',
            'institutionMedicalProcedureForm[status]' => 0
        );
        
        // test submit invalid form
        $crawler = $client->submit($form, $invalidValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value is not valid")')->count(), 'Expecting validation error message "This value is not valid"');
        
        $validValues = array('institutionMedicalProcedureForm[treatmentProcedure]' => 1,
            'institutionMedicalProcedureForm[description]' => 'sdfasdfasdf',
            'institutionMedicalProcedureForm[status]' => 1
        );
        $crawler = $client->submit($form, $validValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $client->getResponse()->headers->get('content-type'));
        $content = \json_decode($client->getResponse()->getContent(),true);
        $this->assertEquals('/institution/medical-centers/edit/1?imptId=1',$content['redirect_url']);
    }
    
    /**
     * @depends testAddMedicalProcedure
     */
    public function testEditMedicalProcedure()
    {
        $uri = '/institution/medical-procedure-types/1/edit-procedure/1';
        // test accessing with no user
        $client = $this->requestUrlWithNoLoggedInUser($uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client), 'Expecting redirection to login page');
        
        $client = $this->getBrowserWithActualLoggedInUser();
        
        //test with invalid impId
        $client->request('GET', '/institution/medical-procedure-types/1/edit-procedure/999999999');
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'Expecting 404 error for invalid impId');
    }
    
    public function testSaveMedicalProcedure()
    {
        $uri = '/institution/medical-procedure-types/1/add-procedure';
        $client = $this->getBrowserWithActualLoggedInUser();
        
        // test invalid id
        $client->request('POST', '/institution/medical-procedure-types/999999999999/add-procedure');
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'Expecting not found error for invalid imptId');
        
    }
}