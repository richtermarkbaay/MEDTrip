<?php
/**
 * Unit test for InstitutionService
 * 
 * @author Alnie Jacobe
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Tests\Services;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleTestCase;

class InstitutionServiceTest extends InstitutionBundleTestCase
{
	/**
	 *
	 * @var HealthCareAbroad\InstitutionBundle\Services\InstitutionService
	 */
	protected $service;
	public function setUp()
	{
		$this->service = new InstitutionService();
		$this->service->setDoctrine($this->getDoctrine());
		
	}
	
	public function tearDown()
	{
		$this->service = null;
	}
	
	public function testCreateInstitution()
	{
		
		$result = $this->service->createInstitution('Vickiii Bellooo', 'whitening', 'slug');
		$this->assertNotEmpty($result);
	}
	
}