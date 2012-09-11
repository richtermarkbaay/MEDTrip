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
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Country")')->count()); // look for the Country text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Address1")')->count()); // look for the New email text
		
		
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
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $formValues);
		$this->assertEquals(302, $client->getResponse()->getStatusCode()); 
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
        		'institution[email]' => 'kristenstewart@yahoo.com',
        		'institution[new_password]' => $this->userPassword,
        		'institution[confirm_password]' => $this->userPassword,
        );
        
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