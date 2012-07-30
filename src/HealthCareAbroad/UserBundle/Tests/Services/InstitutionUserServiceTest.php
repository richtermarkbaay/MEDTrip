<?php
/**
 * Unit test for InstitutionUserService
 * 
 * @author Allejo Chris G. Velarde
 * @author Alnie Jacobe
 */
namespace HealthCareAbroad\UserBundle\Tests\Services;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\UserBundle\Services\InstitutionUserService;

use HealthCareAbroad\UserBundle\Tests\UserBundleTestCase;

class InstitutionUserServiceTest extends UserBundleTestCase
{
    protected $service;
	
    private $nonFixedEmailUser = 'test.institution-user-non-fixed@chromedia.com';
    private $commonPassword = '123456';
    
	public function setUp()
	{
		$this->service = new InstitutionUserService();
		$this->service->setDoctrine($this->getDoctrine());
		$this->service->setChromediaRequest($this->getServiceContainer()->get('services.chromedia_request'));
		$this->service->setChromediaAccountsUri($this->getServiceContainer()->getParameter('chromedia_accounts_uri'));
	}
	
	public function tearDown()
	{
		$this->service = null;
	}
	
	/**
	 * @author Allejo Chris Velarde
	 */
	public function testCreate()
	{
	    $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find(1);
	    $institutionUserType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find(1);
	    
	    $user = new InstitutionUser();
	    
	    $user->setEmail($this->nonFixedEmailUser);
	    $user->setPassword('123456');
	    $user->setFirstName('Test Institution');
	    $user->setMiddleName('M.');
	    $user->setLastName('User');
	    $user->setInstitution($institution);
	    $user->setInstitutionUserType($institutionUserType);
	    
	    $user = $this->service->create($user);
	    
	    return $user;
	}
	
	/**
	 * @author Allejo Chris Velarde
	 */
	public function testLogin()
	{
	    $this->markTestIncomplete();
	}
	
	/**
	 * @depends testCreate
	 */
	public function testUpdate($user)
	{
	    // create temporary 10 character password
		$temporaryPassword = \substr(SecurityHelper::hash_sha256(time()), 0, 10);
		
		// get data for institution
		$institution = $this->doctrine->getRepository('InstitutionBundle:Institution')->find(4);
		
		//get data for institutionUserType
		$institutionUserType = $this->doctrine->getRepository('UserBundle:InstitutionUserType')->find(1);
		
		$user = new InstitutionUser();
		$user->setInstitution($institution);
		$user->setInstitutionUserType($institutionUserType);
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
		
		// get data for institution
		$institution = $this->doctrine->getRepository('InstitutionBundle:Institution')->find(4);
		
		//get data for institutionUserType
		$institutionUserType = $this->doctrine->getRepository('UserBundle:InstitutionUserType')->find(1);
		
		$user = new InstitutionUser();
		$user->setInstitution($institution);
		$user->setInstitutionUserType($institutionUserType);
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