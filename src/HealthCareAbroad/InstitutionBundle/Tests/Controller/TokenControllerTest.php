<?php
/**
 * Functional test for TokenController
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class TokenControllerTest extends InstitutionBundleWebTestCase
{
	public function testConfirmInvitationToken()
	{
		//test for existing active token
		$client = static::createClient();
		$uri = '/confirm/t=94f348d1f65c54cae854b22e5fcc949b408da4682efd9567a66fdbe8323595b7';
		$crawler = $client->request('GET', $uri);		
		$this->assertEquals('', $client->getResponse()->headers->get('location'));
		
		//test not existing token
		$client = static::createClient();
		$uri = '/confirm/t=094f348d1f65c54cae854b22e5fcc949b408da4682efd9567a66fdbe8323595b7';
		$crawler = $client->request('GET', $uri);
		$this->assertEquals('', $client->getResponse()->headers->get('location'));
		
	}
	
	public function testCreate()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/createtoken');
		
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Email")')->count()); // look for the New email text
		
		$form = $crawler->selectButton('Submit')->form();
		
		$formValues = array(
				'form[name]' => 'alnie jacobe1',
				'form[email]' => 'test@yahoo.com1',
		);
		$crawler = $client->submit($form, $formValues);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Invitation sent to test@yahoo.com1")')->count());
		 
		
	}
	
}