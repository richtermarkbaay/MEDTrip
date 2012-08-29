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

class InstitutionControllerTest extends InstitutionBundleWebTestCase
{
	public function testInviteInstitution()
	{
		$url = '/invite-institution';
		
		$client = static::createClient();
		$crawler = $client->request('GET', $url);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Email")')->count()); // look for the Current email text
		
		$form = $crawler->selectButton('submit')->form();
		
		$formValues = array(
				'institutionInvitation[name]' => 'alnie jacobe',
				'institutionInvitation[email]' => 'test@yahoo.com',
		);
		$crawler = $client->submit($form, $formValues);
		$this->assertEquals(200, $client->getResponse()->getStatusCode()); 
		$this->assertGreaterThan(0,$crawler->filter('html:contains("Invitation sent to test@yahoo.com")')->count());
		
		//test for missing email field
		$invalidValues = $formValues;
		$invalidValues['institutionInvitation[email]'] = null;
		$crawler = $client->submit($form, $invalidValues);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
		
		//test for missing email field
		$invalidValues = $formValues;
		$invalidValues['institutionInvitation[name]'] = null;
		$crawler = $client->submit($form, $invalidValues);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
		
	}
	public function testEditInformation()
	{ 
		$editAccountUrl = '/institution/edit-information';
		
		//---- test that this should not be accessed by anonymous user
		$client = $this->requestUrlWithNoLoggedInUser($editAccountUrl);
		$this->assertEquals(302, $client->getResponse()->getStatusCode());
		$redirectLocation = $client->getResponse()->headers->get('location');
		$this->assertTrue($redirectLocation=='/institution/location' || $redirectLocation == 'http://localhost/institution/login');
		//---- end test that this should not be accessed by anonymous user
		
		 //---- test edit logged in account
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $editAccountUrl);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
		
        $formValues = array(
        		'institutionDetail[name]' => 'edit name',
        		'institutionDetail[description]' => 'edit desc',
        		'institutionDetail[country]' => '1',
        		'institutionDetail[city]' => '1',
        		'institutionDetail[address1]' => 'edit address1',
        		'institutionDetail[address2]' => 'edit address2',
        );
		
        $invalidFormValues = $formValues;
        $invalidFormValues['institutionDetail[name]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
           
        //test for invalid description
        $invalidFormValues = $formValues;
        $invalidFormValues['institutionDetail[description]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
         
        //test for invalid address1
        $invalidFormValues = $formValues;
        $invalidFormValues['institutionDetail[address1]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
         
        //test for invalid address2
        $invalidFormValues = $formValues;
        $invalidFormValues['institutionDetail[address2]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
         
        //test valid values
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $editAccountUrl);
        $crawler = $client->submit($form, $formValues);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Successfully updated account")')->count());
        //---- end test edit logged in account
        
		$client = $this->getBrowserWithActualLoggedInUser();
		$client->request('GET', $editAccountUrl."/210");
		$this->assertEquals(404, $client->getResponse()->getStatusCode());
		
		
	}
	
	public function testLoadCities()
	{
		$client = static::createClient();
		$client->request('GET', "location/load-cities/1");
		$this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
		
	}
	public function testSignUp()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/sign-up');
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Description")')->count()); // look for the New email text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Country")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("City")')->count()); // look for the New email text
		
		$form = $crawler->selectButton('submit')->form();

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
		$crawler = $client->submit($form, $formValues);
		$this->assertEquals(302, $client->getResponse()->getStatusCode()); // test that it has been redirected to edit information page
        $this->assertEquals('/institution/edit-information', $client->getResponse()->headers->get('location'));
        
        // test for missing fields flow
        $crawler = $client->request('GET', '/sign-up');
        $formValues = array(
        		'institution[name]' => 'alnie jacobe',
        		'institution[description]' => 'test test',
        		'institution[country]' => '1',
        		'institution[city]' => '1',
        		'institution[address1]' => 'ohuket city',
        		'institution[address2]' => 'ohuket city',
        		'institution[firstName]' => 'test name',
        		'institution[middleName]' => 'middle',
        		'institution[lastName]' => '',
        		'institution[email]' => 'test@yahoo.com',
        		'institution[new_password]' => $this->userPassword,
        		'institution[confirm_password]' => $this->userPassword,
        );
        
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
    
        //test for existing email provided
        $client = static::createClient();
        $crawler = $client->request('GET', '/sign-up');
        $formValues = array(
        		'institution[name]' => 'alnie jacobe',
        		'institution[description]' => 'test test',
        		'institution[country]' => '1',
        		'institution[city]' => '1',
        		'institution[address1]' => 'ohuket city',
        		'institution[address2]' => 'ohuket city',
        		'institution[firstName]' => 'test name',
        		'institution[middleName]' => 'middle',
        		'institution[lastName]' => 'jacobe',
        		'institution[email]' => 'alnie.jacobe@chromedia.com',
        		'institution[new_password]' => $this->userPassword,
        		'institution[confirm_password]' => $this->userPassword,
        );
        
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
	}

}