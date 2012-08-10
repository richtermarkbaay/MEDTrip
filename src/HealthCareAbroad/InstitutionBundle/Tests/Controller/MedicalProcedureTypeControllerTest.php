<?php 

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class MedicalProcedureTypeControllerTest extends InstitutionBundleWebTestCase
{	
	public function testLoadProcedures()
	{
		$client = $this->getBrowserWithActualLoggedInUser();
		$params = array('institution_id' => 1, 'procedure_type_id' => 1);
		$crawler = $client->request('GET', '/institution/procedure-type/load-procedures', $params);

		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}
	
}