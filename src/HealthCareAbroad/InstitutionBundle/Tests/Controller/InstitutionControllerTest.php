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
		$this->assertGreaterThan(0, $crawler->filter('h1:contains("Edit my account")')->count(), "Cannot find the 'Edit my account' header text");
		
		$formValues = array(
				'userAccountDetail[firstName]' => 'Edited firstName',
				'userAccountDetail[middleName]' => 'Edited middleName',
				'userAccountDetail[lastName]' => 'Edited lastName',
		);
		
		$invalidFormValues = $formValues;
		$invalidFormValues['userAccountDetail[firstName]'] = null;
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
		
		$crawler = $client->request('GET', $editAccountUrl);
		$referer = $client->getRequest()->headers->get('referer');
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $formValues);
		$this->assertEquals(302, $client->getResponse()->getStatusCode()); // test that it has been redirected to the referer
		$this->assertEquals($referer, $client->getResponse()->headers->get('location'));
		//---- end test edit logged in account
		
		//---- test edit invalid account
		$client->request('GET', $editAccountUrl.'/12345678234');
		$this->assertEquals(404, $client->getResponse()->getStatusCode());
	}
		
	public function testSignUp()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/signUp');
		
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Description")')->count()); // look for the New email text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Country")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("City")')->count()); // look for the New email text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Address1")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Firstname")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Middlename")')->count()); // look for the New email text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Lastname")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Email")')->count()); // look for the New email text
        $this->assertGreaterThan(0, $crawler->filter('html:contains("New Password")')->count()); // look for the New Password text
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Confirm Password")')->count()); //look for the Confirm Password text
		
		$form = $crawler->selectButton('submit')->form();

		$formValues = array(
				'institution[name]' => 'alnie jacobe',
				'institution[description]' => 'test test',
				'institution[country]' => '1',
				'institution[city]' => '1',
				'institution[address1]' => 'ohuket city',
				'institution[firstName]' => 'test name',
				'institution[middleName]' => 'middle',
				'institution[lastName]' => 'last',
				'institution[email]' => 'test@yahoo.com',
				'institution[new_password]' => $this->userPassword,
				'institution[confirm_password]' => $this->userPassword,
		);
		$crawler = $client->submit($form, $formValues);
		$this->assertEquals(302, $client->getResponse()->getStatusCode()); // test that it has been redirected to homepage
        $this->assertEquals('/institution', $client->getResponse()->headers->get('location'));
        
        // test for missing fields flow
        $crawler = $client->request('GET', '/signUp');
        $formValues = array(
        		'institution[name]' => 'alnie jacobe',
        		'institution[description]' => 'test test',
        		'institution[country]' => '1',
        		'institution[city]' => '1',
        		'institution[address1]' => 'ohuket city',
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
        $crawler = $client->request('GET', '/signUp');
        $formValues = array(
        		'institution[name]' => 'alnie jacobe',
        		'institution[description]' => 'test test',
        		'institution[country]' => '1',
        		'institution[city]' => '1',
        		'institution[address1]' => 'ohuket city',
        		'institution[firstName]' => 'test name',
        		'institution[middleName]' => 'middle',
        		'institution[lastName]' => 'jacobe',
        		'institution[email]' => 'kristenstewart@yahoo.com',
        		'institution[new_password]' => $this->userPassword,
        		'institution[confirm_password]' => $this->userPassword,
        );
        
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        
	}

}