<?php
/**
 * Unit test for InstitutionService
 * 
 * @author Alnie Jacobe
 *
 */
namespace HealthCareAbroad\HelperBundle\Tests\Services;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;
use HealthCareAbroad\HelperBundle\Tests\HelperBundleTestCase;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleTestCase;
use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

class InstitutionServiceTest extends HelperBundleTestCase
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
	
	public function testCreateInstitution()
	{
		$institution = new Institution();
		$institution->setName('alnie');
		$institution->setDescription('test');
		$institution->setSlug('test');
		$institution->setStatus(1);
		$institution->setAddress1('cebu');
		$institution->setAddress2('compostela');
		$institution->setLogo('logo.jpg');
		$institution->setCityId('1');
		$institution->setCountryId('1');
		
		$result = $this->service->createInstitution($institution);
		$this->assertNotEmpty($result);
	}
	
}