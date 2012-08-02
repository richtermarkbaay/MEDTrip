<?php
/**
 * Unit test for InstitutionService
 * 
 * @author Alnie Jacobe
 *
 */
namespace HealthCareAbroad\HelperBundle\Tests\Services;

use HealthCareAbroad\HelperBundle\Tests\HelperBundleTestCase;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleTestCase;

class InstitutionServiceTest extends HelperBundleTestCase
{
	/**
	 *
	 * @var HealthCareAbroad\InstitutionBundle\Services\InstitutionService
	 */
	protected $service;
	public function setUp()
	{
		$this->service = new InstitutionService(parent::$container->get('doctrine'));
		
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