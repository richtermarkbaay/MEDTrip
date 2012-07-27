<?php
/**
 * Unit test for ProviderUserService
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\UserBundle\Tests\Services;

use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\UserBundle\Tests\UserBundleTestCase;

use HealthCareAbroad\UserBundle\Services\ProviderUserService;
use HealthCareAbroad\UserBundle\Services\UserService;

class ProviderUserServiceTest extends UserBundleTestCase
{
	/**
	 *
	 * @var HealthCareAbroad\UserBundle\Services\ProviderUserService
	 */
	protected $service;
	
	public function setUp()
	{
		$this->service = new ProviderUserService(parent::$container->get('doctrine'));
		$this->doctrine = parent::$container->get('doctrine');
		$this->service->setChromediaRequest(parent::$container->get('services.chromedia_request'));
		$this->service->setChromediaAccountsUri('http://accounts.chromedia.local/app_dev.php');
	}
	
	public function tearDown()
	{
		$this->service = null;
	}
	
	public function testUpdate()
	{
		// create temporary 10 character password
		$temporaryPassword = \substr(SecurityHelper::hash_sha256(time()), 0, 10);
		
		// get data for provider
		$provider = $this->doctrine->getRepository('ProviderBundle:Provider')->find(4);
		
		//get data for providerUserType
		$providerUserType = $this->doctrine->getRepository('UserBundle:ProviderUserType')->find(1);
		
		$user = new ProviderUser();
		$user->setProvider($provider);
		$user->setProviderUserType($providerUserType);
		$user->setEmail('aj@chromedia.com');
		$user->setPassword($temporaryPassword);
		$user->setFirstName('alnie');
		$user->setMiddleName('leones');
		$user->setLastName('jacobe');
		$user->setStatus('1');
		$returnValue = $this->service->update($user, 4);
		$this->assertTrue($returnValue);
		return $returnValue;
		
	}
	
	public function testChangePassword()
	{
		// create password
		$password = SecurityHelper::hash_sha256('123456');
		
		// get data for provider
		$provider = $this->doctrine->getRepository('ProviderBundle:Provider')->find(4);
		
		//get data for providerUserType
		$providerUserType = $this->doctrine->getRepository('UserBundle:ProviderUserType')->find(1);
		
		$user = new ProviderUser();
		$user->setProvider($provider);
		$user->setProviderUserType($providerUserType);
		$user->setEmail('aj@chromedia.com');
		$user->setFirstName('alnie');
		$user->setMiddleName('leones');
		$user->setLastName('jacobe');
		$user->setStatus('1');
		
		$returnValue = $this->service->changePassword($user, 4, $password);
		$this->assertTrue($returnValue);
		return $returnValue;
	}
	
	public function testFindIdandPassword()
	{
		// create password
		$password = SecurityHelper::hash_sha256('123456');
		
		$returnValue = $this->service->findByIdAndPassword('4', $password);
		$this->assertNotEmpty($returnValue);
		return $returnValue;
	}
	
	public function testFindEmailandPassword()
	{
		// create password
		$password = SecurityHelper::hash_sha256('123456');
		
		$returnValue = $this->service->findByEmailAndPassword('alnie.jacobe@chromedia.com', $password);
		$this->assertNotEmpty($returnValue);
		return $returnValue;
	}
	
	public function testFindbyId()
	{
		$returnValue = $this->service->findById('4',TRUE);
		$this->assertNotEmpty($returnValue);
	}
	
}