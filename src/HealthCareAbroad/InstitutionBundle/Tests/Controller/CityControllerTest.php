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
	public function testLoadCities()
	{
		$client = static::createClient();
		$client->request('GET', "location/load-cities/1");
		$this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
	
	}
}