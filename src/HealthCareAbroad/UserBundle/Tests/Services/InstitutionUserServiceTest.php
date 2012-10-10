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
		$this->service->setEventDispatcher($this->getServiceContainer()->get('event_dispatcher'));
		$this->service->setEventFactory($this->getServiceContainer()->get('events.factory'));
		$this->service->setDoctrine($this->getDoctrine());
		$this->service->setChromediaRequest($this->getServiceContainer()->get('services.chromedia_request'));
		$this->service->setChromediaAccountsUri($this->getServiceContainer()->getParameter('chromedia_accounts_uri'));
		$this->service->setSecurityContext($this->getServiceContainer()->get('security.context'));
	}
	
	public function tearDown()
	{
		$this->service = null;
	}
	
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
	    $this->assertTrue($user->getAccountId() != 0);
	    
	    $savedUser = $this->getDoctrine()->getRepository('UserBundle:InstitutionUser')->find($user->getAccountId());
	    $this->assertNotNull($savedUser);
	    
	    return $user;
	}
	
	/**
	 * @expectedException HealthCareAbroad\UserBundle\Services\Exception\FailedAccountRequestException
	 */
	public function testCreateWithMissingFields()
	{
	    $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find(1);
	    $institutionUserType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find(1);
	     
	    $user = new InstitutionUser();
	     
	    $user->setEmail(null);
	    $user->setPassword($this->commonPassword);
	    $user->setFirstName('');
	    $user->setMiddleName('');
	    $user->setLastName('User');
	    $user->setInstitution($institution);
	    $user->setInstitutionUserType($institutionUserType);
	    $user->setStatus(SiteUser::STATUS_ACTIVE);
	     
	    $user = $this->service->create($user);
	    return $user;
	}
	
	/**
	 * @depends testCreate
	 * @param 
	 */
	public function testLogin(InstitutionUser $user)
	{
	    // set the session
	    $this->service->setSession($this->getServiceContainer()->get('session'));
	    
	    $isLoginOk = $this->service->login($user->getEmail(), $this->commonPassword);
	    $this->assertTrue($isLoginOk, 'Unable to login as InstitutionUser using credential '."{$user->getEmail()}::{$this->commonPassword}");
	    
	    return $user;
	}
	
	/**
	 * @depends testLogin
	 * @param InstitutionUser $user
	 */
	public function testFailedLogin(InstitutionUser $user)
	{
	    // set the session
	    $this->service->setSession($this->getServiceContainer()->get('session'));
	    
	    $isLoginOk = $this->service->login($user->getEmail(), $this->commonPassword.'123456');
	    $this->assertFalse($isLoginOk);
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
	 * @expectedException HealthCareAbroad\UserBundle\Services\Exception\InvalidInstitutionUserOperationException
	 */
	public function testUpdateWithNoAccountId()
	{
	    $user = new InstitutionUser();
	    $this->service->update($user);
	}
	
	/**
	 * @depends testCreate
	 * @expectedException HealthCareAbroad\UserBundle\Services\Exception\FailedAccountRequestException
	 * @param InstitutionUser $user
	 */
	public function testUpdateWithFailedRequest(InstitutionUser $institutionUser)
	{
	    $institutionUser->setFirstName('');
	    $updatedUser = $this->service->update($institutionUser);
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
		
		// test for wrong password
		$retrievedUser = $this->service->findByIdAndPassword($user->getAccountId(), $this->commonPassword.'1232143244');
		$this->assertNull($retrievedUser);
	}
	
	/**
	 * @depends testCreate
	 * @param InstitutionUser $user
	 */
	public function testFindEmailandPassword(InstitutionUser $user)
	{
		$email = $user->getEmail();
		
		$retrievedUser = $this->service->findByEmailAndPassword($email, $this->commonPassword);
		
		$this->assertNotNull($retrievedUser);
		$this->assertEquals($user->getEmail(), $user->getEmail());
		$this->assertEquals($user->getPassword(), $user->getPassword());
		
        // test for an admin user email
        $retrievedUser = $this->service->findByEmailAndPassword('test.adminuser@chromedia.com', $this->commonPassword);
        $this->assertNull($retrievedUser); // this should be null since retrieved user is not InstitutionUser
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
		
		// test invalid id 
		$retrievedUser = $this->service->findById(time());
		$this->assertNull($retrievedUser);
	}
}