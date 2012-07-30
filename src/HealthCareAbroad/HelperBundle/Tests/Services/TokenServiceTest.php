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
		$this->service = new TokenService(parent::$container->get('doctrine'));
	}
	
	public function tearDown()
	{
		$this->service = null;
	}
	
	public function testGetActiveInstitutionInvitationByToken()
	{
		$token = "46eff89e1b51895f101e56c71ff12f9cfbc9fd98b582adfb4dd21848ab564e6c";
		$institution = $this->service->getActiveInstitutionInvitationByToken($token);
		$this->assertNotEmpty($institution);
		
		return $institution;
	}
	
	public function testGetActiveInstitutionUserInvitatinByToken()
	{
		$token = "7778a0cb59cf98c794b3300f77a3d79a6d75bdcebe1ce13aecab741f1f02e958";
		$institutionUser = $this->service->getActiveInstitutionUserInvitatinByToken($token);
		$this->assertEmpty($institutionUser);
	
		return $institutionUser;
	}
}