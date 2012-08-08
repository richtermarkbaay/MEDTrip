<?php
/**
 * Unit test for TokenService
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\HelperBundle\Tests\Services;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\HelperBundle\Tests\HelperBundleTestCase;

use HealthCareAbroad\HelperBundle\Services\TokenService;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;

class TokenServiceTest extends HelperBundleTestCase
{
	/**
	 *
	 * @var HealthCareAbroad\HelperBundle\Services\TokenService
	 */
	protected $service;
	
	public function setUp()
	{
		$this->service = new TokenService($this->getServiceContainer()->get('doctrine'));
	}
	
	public function tearDown()
	{
		$this->service = null;
	}
	
	public function testGetActiveInstitutionInvitationByToken()
	{			  
		$token = "94f348d1f65c54cae854b22e5fcc949b408da4682efd9567a66fdbe8323595b7";
		$institution = $this->service->getActiveInstitutionInvitationByToken($token);
		$this->assertNotEmpty($institution);
		
		return $institution;
	}
	
	public function testGetActiveInstitutionUserInvitationByToken()
	{
		$token = "94f348d1f65c54cae854b22e5fcc949b408da4682efd9567a66fdbe8323595b7";
		$institutionUser = $this->service->getActiveInstitutionUserInvitationByToken($token);
		$this->assertNotEmpty($institutionUser);
	
		return $institutionUser;
	}
}