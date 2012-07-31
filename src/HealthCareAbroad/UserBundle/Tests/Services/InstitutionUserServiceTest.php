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
	    $user->setPassword($this->commonPassword);
	    $user->setFirstName('Test Institution');
	    $user->setMiddleName('M.');
	    $user->setLastName('User');
	    $user->setInstitution($institution);
	    $user->setInstitutionUserType($institutionUserType);
	    $user->setStatus(SiteUser::STATUS_ACTIVE);
	    
	    $user = $this->service->create($user);
	    return $user;
	}
	
	/**
	 * @author Allejo Chris Velarde
	 * @depends testCreate
	 * @param 
	 */
	public function testLogin(InstitutionUser $user)
	{
	    // set the session
	    $this->service->setSession($this->getServiceContainer()->get('session'));
	    
	    $isLoginOk = $this->service->login($user->getEmail(), $this->commonPassword);
	    $this->assertTrue($isLoginOk, 'Unable to login as InstitutionUser using credential '."{$user->getEmail()}::{$this->commonPassword}");
	}
	
	/**
	 * @depends testCreate
	 * @param HealthCareAbroad\UserBundle\Entity\InstitutionUser
	 */
	public function testUpdate(InstitutionUser $user)
	{
	    
	    $user->setFirstName($user->getFirstName().' - Updated');
		$user->setMiddleName($user->getMiddleName(). ' - Updated');
		$user->setLastName($user->getLastName(). '- Updated');
		
		$updatedUser = $this->service->update($user);
		
		$this->assertEquals($updatedUser->getFirstName(), $user->getFirstName(), "Update of first name failed");
		$this->assertEquals($updatedUser->getMiddleName(), $user->getMiddleName(), "Update of middle name failed");
		$this->assertEquals($updatedUser->getLastName(), $user->getLastName(), "Update of last name failed");
		
		
	}
	
	/**
	 * @depends testCreate
	 * @param InstitutionUser $user
	 */
	public function testFindIdAndPassword(InstitutionUser $user)
	{
		$retrievedUser = $this->service->findByIdAndPassword($user->getAccountId(), $this->commonPassword);
		
		$this->assertEquals($retrievedUser->getAccountId(), $user->getAccountId());
		$this->assertEquals($retrievedUser->getPassword(), SecurityHelper::hash_sha256($this->commonPassword));
	}
	
	/**
	 * @depends testCreate
	 * @param InstitutionUser $user
	 */
	public function testFindEmailandPassword(InstitutionUser $user)
	{
		$email = $user->getEmail();
		
		$retrievedUser = $this->service->findByEmailAndPassword($email, $this->commonPassword);
		
		$this->assertNotNull($retrievedUser, "No InstitutionUser with email = {$email} and password = {$this->commonPassword}");
	}
	
	/**
	 * @depends testCreate
	 * @param InstitutionUser $user
	 */
	public function testFindbyId(InstitutionUser $user)
	{
	    $id = $user->getAccountId();
		$retrievedUser = $this->service->findById($id);
		$this->assertNotNull($retrievedUser, "No InstitutionUser with AccountId = {$id}");
	}
}