<?php
/**
 * Unit test for InstitutionService
 * 
 * @author Alnie Jacobe
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Tests\Services;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;
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
	
	
	
	public function testCreateInstitution()
	{
		//get data for city
		$city = $this->doctrine->getRepository('HelperBundle:City')->find(1);
		
		//get data for country
		$country = $this->doctrine->getRepository('HelperBundle:Country')->find(1);
		
		$institution = new Institution();
		$institution->setName('alnie');
		$institution->setDescription('test');
		$institution->setSlug('test');
		$institution->setStatus(1);
		$institution->setAddress1('cebu');
		$institution->setAddress2('compostela');
		$institution->setLogo('logo.jpg');
		$institution->setCity($city);
		$institution->setCountry($country);
		
		$result = $this->service->create($institution);
		$this->assertNotEmpty($result);
		
	}
	
	public function testUpdateInstitution()
	{
		//test for valid institution
		$institutionValue = new Institution();
		$institutionValue = $this->doctrine->getRepository('InstitutionBundle:Institution')->find(1);
	
		$institutionValue->setDescription('edited description');
	
		$result = $this->service->updateInstitution($institutionValue);
		$this->assertNotEmpty($result);
	
	}
	
}