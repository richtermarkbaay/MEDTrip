<?php
/**
 * Functional test for InstitutionController
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InstitutionSignUpControllerTest extends InstitutionBundleWebTestCase
{
	
	
	public function testSignUp()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/sign-up');
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count()); // look for the Name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Description")')->count()); // look for the Description text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Password")')->count()); // look for the Country text
		//$this->assertGreaterThan(0, $crawler->filter('html:contains("USA")')->count()); // look for the New Address1 text
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$formValues = array(
				'institution[name]' => 'alnie jacobe',
				'institution[description]' => 'test test',
				'institution[country]' => '1',
				'institution[city]' => '1',
				'institution[address1]' => 'ohuket city',
				'institution[address2]' => 'ohuket city',
				'institution[firstName]' => 'test name',
				'institution[middleName]' => 'middle',
				'institution[lastName]' => 'last',
				'institution[email]' => 'test@yahoo.com',
				'institution[new_password]' => $this->userPassword,
				'institution[confirm_password]' => $this->userPassword,
		);
		
		//test for existing email provided
		$invalidFormValues = $formValues;
		$invalidFormValues['institution[email]'] = 'kristenstewart@yahoo.com';
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $invalidFormValues);
		$this->assertEquals(500, $client->getResponse()->getStatusCode());
		$duplicateNotCreated = \trim($crawler->filter('div.text_exception > h1')->text()) != "SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry";
		$this->assertTrue($duplicateNotCreated, 'institution_medical_procedure_types Duplicate Entry.');
		
		
        // test for missing name field
		$client = static::createClient();
		$crawler = $client->request('GET', '/sign-up');
        $invalidFormValues = $formValues;
        $invalidFormValues['institution[name]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
        //test for missing description
        $invalidFormValues = $formValues;
        $invalidFormValues['institution[description]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
                
        //test for missing address 1
        $invalidFormValues = $formValues;
        $invalidFormValues['institution[address1]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
                
        //test for missing address 2
        $client = static::createClient();
        $crawler = $client->request('GET', '/sign-up');
        $invalidFormValues = $formValues;
        $invalidFormValues['institution[address2]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
                
        //test for missing firstname
        $invalidFormValues = $formValues;
        $invalidFormValues['institution[firstName]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
                
        //test for missing middlename
        $client = static::createClient();
        $crawler = $client->request('GET', '/sign-up');
        $invalidFormValues = $formValues;
        $invalidFormValues['institution[middleName]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
                
        //test for missing lastname
        $invalidFormValues = $formValues;
        $invalidFormValues['institution[lastName]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
      
        $client = static::createClient();
        $crawler = $client->request('GET', '/sign-up');
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
        
	}
	
	public function testInviteInstitution()
	{
		$url = '/invite-institution';
	
		$client = static::createClient();
		$crawler = $client->request('GET', $url);
			$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Email")')->count()); // look for the Current email text
	
		$formValues = array(
				'institutionInvitation[name]' => 'alnie jacobe',
				'institutionInvitation[email]' => 'test@yahoo.com',
		);
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $formValues);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0,$crawler->filter('html:contains("Invitation sent to test@yahoo.com")')->count());
	
		//test for missing email field
		$invalidValues = $formValues;
		$invalidValues['institutionInvitation[email]'] = null;
		$crawler = $client->submit($form, $invalidValues);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
	
		//test for missing name field
		$invalidValues = $formValues;
		$invalidValues['institutionInvitation[name]'] = null;
		$crawler = $client->submit($form, $invalidValues);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
	
	}
}