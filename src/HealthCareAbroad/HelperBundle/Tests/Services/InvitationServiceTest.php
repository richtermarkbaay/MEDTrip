<?php
/**
 * Unit test for InvitationService
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\HelperBundle\Tests\Services;

use HealthCareAbroad\ProviderBundle\Entity\ProviderInvitation;

use HealthCareAbroad\ProviderBundle\Entity\Provider;

use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use HealthCareAbroad\UserBundle\Entity\ProviderUserType;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\HelperBundle\Tests\HelperBundleTestCase;

use HealthCareAbroad\HelperBundle\Services\InvitationService;

use HealthCareAbroad\HelperBundle\Entity\InvitationToken;

class InvitationServiceTest extends HelperBundleTestCase
{
	/**
	 *
	 * @var HealthCareAbroad\HelperBundle\Services\InvitationService
	 */
	protected $service;
	protected $doctrine;
	protected $twig;
	public function setUp()
	{
		$this->service = new InvitationService(parent::$container->get('doctrine'));
		$this->doctrine = parent::$container->get('doctrine');
		$this->service->setTwig(parent::$container->get('twig'));
		$this->service->setMailer(parent::$container->get('mailer'));
		
	}
	
	public function tearDown()
	{
		$this->service = null;
		$this->doctrine = null;
		$this->twig = null;
	}	
	
	public function testCreateInvitationToken()
	{
		$invitationToken = $this->service->createInvitationToken(0);
		$this->assertNotEmpty($invitationToken);
		return $invitationToken;
	}
	
	public  function testCreateProviderInvitation()
	{
		$invitation = new ProviderInvitation();
		$invitation->setEmail('alnie@yahoo.com');
		$invitation->setName('alnie jacobe');
		
		$invitationToken = $this->service->createInvitationToken(0);
		$providerInvitation = $this->service->createProviderInvitation($invitation, 'hi from healthcareabroad', $invitationToken);
		$this->assertNotEmpty($providerInvitation);
		return $providerInvitation;
	}
	
	public function testSendProviderUserLoginCredentials()
	{
		// create temporary 10 character password
		$temporaryPassword = \substr(SecurityHelper::hash_sha256(time()), 0, 10);
		
		// get data for provider
		$provider = $this->doctrine->getRepository('ProviderBundle:Provider')->find(8);
		
		//get data for providerUserType
		$providerUserType = $this->doctrine->getRepository('UserBundle:ProviderUserType')->find(1);
		
		$user = new ProviderUser();
		$user->setProvider($provider);
		$user->setProviderUserType($providerUserType);
		$user->setEmail('alnie.jacobe@chromedia.com');
		$user->setPassword($temporaryPassword);
		$user->setFirstName('alnie');
		$user->setMiddleName('leones');
		$user->setLastName('jacobe');
		$user->setStatus('1');
		$sendingResult = $this->service->sendProviderUserLoginCredentials($user, $temporaryPassword);
		$this->assertEquals(1,$sendingResult);
		return $sendingResult;
		
	}
		public function testSendProviderUserInvitation()
		{
			// get data for provider
			$provider = $this->doctrine->getRepository('ProviderBundle:Provider')->find(8);
	
			//get data for providerUserType
			$providerInvitation = $this->doctrine->getRepository('ProviderBundle:ProviderUserInvitation')->find(1);
	
			$sendingResult = $this->service->sendProviderUserInvitation($provider, $providerInvitation);
			$this->assertEquals(1,$sendingResult);
			return $sendingResult;
	
		}
	
}