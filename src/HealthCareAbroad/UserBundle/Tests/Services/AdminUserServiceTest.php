<?php
/**
 * Unit test for AdminUserService class
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\UserBundle\Tests\Services;
use HealthCareAbroad\UserBundle\Entity\SiteUser;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

use HealthCareAbroad\UserBundle\Services\AdminUserService;

use HealthCareAbroad\UserBundle\Tests\UserBundleTestCase;

class AdminUserServiceTest extends UserBundleTestCase
{
    /**
     * @var AdminUserService
     */
    protected $service;
    
    private $nonFixedEmailUser = 'test.admin-user-non-fixed@chromedia.com';
    private $commonPassword = '123456';
    
    public function setUp()
    {
        $this->service = new AdminUserService();
        $this->service->setEventDispatcher($this->getServiceContainer()->get('event_dispatcher'));
        $this->service->setEventFactory($this->getServiceContainer()->get('events.factory'));
        $this->service->setDoctrine($this->getDoctrine());
        $this->service->setChromediaRequest($this->getServiceContainer()->get('services.chromedia_request'));
        $this->service->setChromediaAccountsUri($this->getServiceContainer()->getParameter('chromedia_accounts_uri'));
        $this->service->setSecurityContext($this->getServiceContainer()->get('security.context'));
    }
    
    public function testCreate()
    {
        $adminUserType = $this->getDoctrine()->getRepository('UserBundle:AdminUserType')->find(1);
        
        $adminUser = new AdminUser();
        $adminUser->setAdminUserType($adminUserType);
        $adminUser->setEmail($this->nonFixedEmailUser);
        $adminUser->setPassword($this->commonPassword);
        $adminUser->setFirstName('Admin1');
        $adminUser->setMiddleName('M1');
        $adminUser->setLastName('AdminLast1');
        $adminUser->setStatus(SiteUser::STATUS_ACTIVE);
        
        $adminUser = $this->service->create($adminUser);
        
        $this->assertTrue($adminUser->getAccountId() != 0);
        
        $savedUser = $this->getDoctrine()->getRepository('UserBundle:AdminUser')->find($adminUser->getAccountId());
        $this->assertNotNull($savedUser);
        
        return $adminUser;
    }
    
    
    /**
     * @depends testCreate
     */
    public function testFindByEmailAndPassword(AdminUser $adminUser)
    {
        $retrievedUser = $this->service->findByEmailAndPassword($adminUser->getEmail(), $this->commonPassword);
        
        $this->assertNotNull($retrievedUser);
        $this->assertEquals($retrievedUser->getEmail(), $adminUser->getEmail());
        $this->assertEquals($retrievedUser->getPassword(), $adminUser->getPassword());
        
        // retrieve invalid password
        $retrievedUser = $this->service->findByEmailAndPassword($adminUser->getEmail(), $this->commonPassword.'1234567');
        $this->assertNull($retrievedUser);
        
        // retrieve email and password for institution user
        $retrievedUser = $this->service->findByEmailAndPassword('test.user@chromedia.com', $this->commonPassword);
        $this->assertNull($retrievedUser);
    }
    
    /**
     * @depends testCreate
     * @param AdminUser $adminUser
     */
    public function testLogin(AdminUser $adminUser)
    {
        // set the session
        $this->service->setSession($this->getServiceContainer()->get('session'));
        $isLoginOk = $this->service->login($adminUser->getEmail(), $this->commonPassword);
        $this->assertTrue($isLoginOk);
        
        // test failed login
        $isLoginOk = $this->service->login($adminUser->getEmail(), $this->commonPassword.'12344444');
        $this->assertFalse($isLoginOk);
    }
    
    /**
     * @depends testCreate
     * @param AdminUser $adminUser
     */
    public function testFindbyId(AdminUser $adminUser)
    {
    	$accountId = $adminUser->getAccountId();
    	$result = $this->service->findById($accountId, true);
    	$this->assertNotEmpty($result);
    }
    
    /**
     * @depends testCreate
     * @param AdminUser $adminUser
     */
	public function testUpdate(AdminUser $adminUser)
	{
		//test for valid adminuser
		$adminUser->setFirstName('edited name');
	
		$result = $this->service->update($adminUser);
		$this->assertNotEmpty($result);
	
		//test for empty adminuser
		$adminUser = new AdminUser();
		$result = $this->service->update($adminUser);
		$this->assertEmpty($result);
	
	}
}