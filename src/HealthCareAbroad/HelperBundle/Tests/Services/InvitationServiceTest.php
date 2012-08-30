<?php
/**
 * Unit test for InvitationService
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\HelperBundle\Tests\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInvitation;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\UserBundle\Entity\InstitutionUserType;

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
		$this->service = new InvitationService($this->getDoctrine());
		$this->doctrine = $this->getDoctrine();
		$this->service->setTwig($this->getServiceContainer()->get('twig'));
		$this->service->setMailer($this->getServiceContainer()->get('mailer'));
		
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
		
	}
	
	public  function testCreateInstitutionInvitation()
	{
		$invitation = new InstitutionInvitation();
		$invitation->setEmail('alnie@yahoo.com');
		$invitation->setName('alnie jacobe');
		
		$institutionInvitation = $this->service->sendInstitutionInvitation($invitation);
		$this->assertNotEmpty($institutionInvitation);
		
		return $institutionInvitation;
	}
	
	public function testSendInstitutionUserLoginCredentials()
	{
		// create temporary 10 character password
		$temporaryPassword = \substr(SecurityHelper::hash_sha256(time()), 0, 10);
		
		// get data for institution
		$institution = $this->doctrine->getRepository('InstitutionBundle:Institution')->find(1);
		
		//get data for institutionUserType
		$institutionUserType = $this->doctrine->getRepository('UserBundle:InstitutionUserType')->find(1);
		
		$user = new InstitutionUser();
		$user->setInstitution($institution);
		$user->setInstitutionUserType($institutionUserType);
		$user->setEmail('alnie.jacobe@chromedia.com');
		$user->setPassword($temporaryPassword);
		$user->setFirstName('alnie');
		$user->setMiddleName('leones');
		$user->setLastName('jacobe');
		$user->setStatus('1');
		$sendingResult = $this->service->sendInstitutionUserLoginCredentials($user, $temporaryPassword);
		$this->assertEquals(1,$sendingResult);
		return $sendingResult;
	}
	public function testSendInstitutionUserInvitation()
	{
		// get data for institution
		$institution = $this->doctrine->getRepository('InstitutionBundle:Institution')->find(1);

		//get data for institutionUserType
		$institutionInvitation = $this->doctrine->getRepository('InstitutionBundle:InstitutionUserInvitation')->find(1);

		$sendingResult = $this->service->sendInstitutionUserInvitation($institution, $institutionInvitation);
		$this->assertEquals(1,$sendingResult);
		return $sendingResult;

	}
	
}