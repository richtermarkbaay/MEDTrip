<?php
/**
 * Unit test for UserService class. Tests here are minimal since most functionality will be tested in detail in InstitutionUserServiceTest and AdminUserServiceTest
 * 
 * @author Allejo Chris G. Velarde
 */


namespace HealthCareAbroad\UserBundle\Tests\Services;

use HealthCareAbroad\UserBundle\Services\UserService;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use HealthCareAbroad\UserBundle\Tests\UserBundleTestCase;

class UserServiceTest extends UserBundleTestCase
{
    /**
     * 
     * @var UserService
     */
    protected $service;
    
    public function setUp()
    {
        $this->service = new UserService();
        $this->service->setDoctrine($this->getDoctrine());
        $this->service->setChromediaRequest($this->getServiceContainer()->get('services.chromedia_request'));
        $this->service->setChromediaAccountsUri($this->getServiceContainer()->getParameter('chromedia_accounts_uri'));
    }
    
    public function tearDown()
    {
        $this->service = null;
    }
    
    public function testGetUserForNewSiteUser()
    {
        $user = new InstitutionUser();
        $result = $this->service->getUser($user);
        $this->assertNull($result);
        
        // test invalid accountId
        $user->setAccountId(99999999);
        $result = $this->service->getUser($user);
        $this->assertNull($result);
    }
} 