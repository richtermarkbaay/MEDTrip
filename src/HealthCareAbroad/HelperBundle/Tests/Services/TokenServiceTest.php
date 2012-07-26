<?php
/**
 * Unit test for TokenService
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\HelperBundle\Tests\Services;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\HelperBundle\Tests\HelperBundleTestCase;

use HealthCareAbroad\HelperBundle\Services\TokenService;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;

class TokenServiceTest extends HelperBundleTestCase
{
	/**
	 *
	 * @var Chromedia\AccountBundle\Services\AccountService
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
	
	public function testGetActiveProviderInvitationByToken()
	{
		$token = "c1913a1ed7780224b505aed516e021f7b9220550a63593bedbdb4ed29c7a18b1";
		$provider = $this->service->getActiveProviderInvitationByToken($token);
		$this->assertNotNull($this->service->getActiveProviderInvitationByToken($token));
		
		return $provider;
	}
	
	public function testGetActiveProviderUserInvitatinByToken()
	{
		$token = "c1913a1ed7780224b505aed516e021f7b9220550a63593bedbdb4ed29c7a18b1";
		$provider = $this->service->getActiveProviderUserInvitatinByToken($token);
		$this->assertNotNull($this->service->getActiveProviderUserInvitatinByToken($token));
	
		return $provider;
	}
}