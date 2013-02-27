<?php
/**
 * Functional test for AdminUserRoleController
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\UserBundle\Entity\AdminUserType;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class AdminUserRoleControllerTest extends AdminBundleWebTestCase
{
    /**
     * 
     * @var HealthCareAbroad\UserBundle\Entity\AdminUserType
     */
    private $userType;
    
    /**
     * @var HealthCareAbroad\UserBundle\Entity\AdminUserRole
     */
    private $userRole;
    
    public function setUp()
    {
        parent::setUp();
        $this->userType = $this->getDoctrine()->getRepository('UserBundle:AdminUserType')->find(2);
        $this->userRole = $this->getDoctrine()->getRepository('UserBundle:AdminUserRole')->find(2);
        
    }   
    
    public function testIndex()
    {
        $uri ='/admin/settings/user-roles';
        
        // test that this should not be acessed by non-authenticated users
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->get('location')=='/admin/location' || $client->getResponse()->headers->get('location') == 'http://localhost/admin/login', 'Expecting redirect to login page and not to '.$client->getResponse()->headers->get('location'));
        
        // test with valid user
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('h4:contains("User Types")')->count(), 'Page heading should contatin "Admin user permissions"');
        
        // test that this must not be accessed with a user with invalid roles
        $client = $this->getBrowserWithMockLoggedUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "Access is forbidden to not allowed roles");
    }
    
    public function testViewByUserType()
    {
        $uri = "/admin/settings/user-type/{$this->userType->getId()}/user-roles";
        
        // test that this should not be acessed by non-authenticated users
        $client = static::createClient();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->get('location')=='/admin/location' || $client->getResponse()->headers->get('location') == 'http://localhost/admin/login', 'Expecting redirect to login page and not to '.$client->getResponse()->headers->get('location'));
        
        // test that this should not be acessed by non-authorized users
        $client = $this->getBrowserWithMockLoggedUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "Expecting 403 Forbidden error after unauthorized access");
        
        // test with valid user
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('h1:contains("Manage permissions for")')->count(), 'Page heading should contatin "Manage permissions for"');
        
        // test with invalid user type
        $crawler = $client->request('GET', "/admin/settings/user-type/1234567899999/user-roles");
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
   
    public function testAddAndRemoveRoleToUserType()
    {
        $uri = '/admin/settings/user-roles/add-to-user-type';
        $params = array('userRoleId' => $this->userRole->getId(), 'userTypeId' => $this->userType->getId());
        
        // test that it will not accept a GET method
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri, $params);
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'Expecting method GET to be not accepted');
        
        // test that this should not be acessed by non-authenticated users
        $client = static::createClient();
        $crawler = $client->request('POST', $uri, $params);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->get('location')=='/admin/location' || $client->getResponse()->headers->get('location') == 'http://localhost/admin/login', 'Expecting redirect to login page and not to '.$client->getResponse()->headers->get('location'));
        
        // test that this should not be acessed by non-authorized users
        $client = $this->getBrowserWithMockLoggedUser();
        $crawler = $client->request('POST', $uri, $params);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "Expecting 403 Forbidden error after unauthorized access");
        
        // test valid post
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', $uri, $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // test that adding same role to user type will throw an error 500
        $crawler = $client->request('POST', $uri, $params);
        $this->assertEquals(500, $client->getResponse()->getStatusCode(), "Expecting error 500 after adding same role to same user type");
        
        // test that adding invalid role to type will throw error 404 
        $crawler = $client->request('POST', $uri, array('userRoleId' => 99999, 'userTypeId' => $this->userType->getId()));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "Expecting error 404 after adding invalid role to user type");
        
        // test that adding invalid type to role will throw error 404
        $crawler = $client->request('POST', $uri, array('userRoleId' => $this->userRole->getId(), 'userTypeId' => 9999999));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "Expecting error 404 after adding role to invalid user type");
        
        //---- end test for adding role to user type ---->
        
        //---- test for removing role from user type ---->
        $uri = '/admin/settings/user-roles/remove-role-from-user-type';
        $params = array('userRoleId' => $this->userRole->getId(), 'userTypeId' => $this->userType->getId());
        
        // test that it will not accept a GET method
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri, $params);
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'Expecting method GET to be not accepted');
        
        // test that this should not be acessed by non-authenticated users
        $client = static::createClient();
        $crawler = $client->request('POST', $uri, $params);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->get('location')=='/admin/location' || $client->getResponse()->headers->get('location') == 'http://localhost/admin/login', 'Expecting redirect to login page and not to '.$client->getResponse()->headers->get('location'));
        
        // test that this should not be acessed by non-authorized users
        $client = $this->getBrowserWithMockLoggedUser();
        $crawler = $client->request('POST', $uri, $params);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), "Expecting 403 Forbidden error after unauthorized access");
        
        // test to remove invalid
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', $uri, array('userRoleId' => 99999, 'userTypeId' => 21312388324242399));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "Expecting error 404 after passing invalid user type and user role");
        
        // test valid data post
        $crawler = $client->request('POST', $uri, $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // test that it has been added by requesting the add again and expecting a 200 response
        $uri = '/admin/settings/user-roles/add-to-user-type';
        $crawler = $client->request('POST', $uri, $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
}