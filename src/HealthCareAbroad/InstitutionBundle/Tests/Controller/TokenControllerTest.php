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
}