<?php
/**
 * Unit test for InstitutionService
 * 
 * @author Alnie Jacobe
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Tests\Services;

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
		$this->service = new InstitutionService($this->getDoctrine());
		
	}
	
	public function tearDown()
	{
		$this->service = null;
	}
	
} 