<?php 

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class MedicalCenterControllerTest extends InstitutionBundleWebTestCase
{
	public function testLoadProcedureTypes()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$params = array('medical_center_id' => 1);
		$crawler = $client->request('GET', '/institution/medical-center/load-procedure-types', $params);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}	
}