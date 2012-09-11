<?php 
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;
use HealthCareAbroad\UserBundle\Entity\InstitutionUserType;

class InstitutionUserRoleController extends InstitutionBundleWebTestCase
{
	/**
	 *
	 * @var HealthCareAbroad\UserBundle\Entity\InstitutionUserType
	 */
	private $userType;
	
	/**
	 * @var HealthCareAbroad\UserBundle\Entity\InstitutionUserRole
	 */
	private $userRole;
	
	public function setUp()
	{
		parent::setUp();
		$this->userType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find(1);
		$this->userRole = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserRole')->find(2);
		
	}
	public function testIndex()
	{
		$client = static::createClient();
		$editAccountUrl = '/institution/staff/user-roles';
		
		// test for correct logged user
	    $client = $this->getBrowserWithActualLoggedInUser();
	    $crawler = $client->request('GET', $editAccountUrl);
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
	}
	
	public function testViewByUserType()
	{
		// test with valid user
		$uri = "/institution/staff/user-type/{$this->userType->getId()}/user-roles";
        
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', $uri);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0,$crawler->filter('h1:contains("Manage permissions for")')->count(), 'Page heading should contatin "Manage permissions for"');
		
		// test that adding invalid role to type will throw error 404
		$crawler = $client->request('GET', $uri, array('id' => 123));
		$this->assertEquals(404, $client->getResponse()->getStatusCode(), "Expecting error 404 after adding invalid role to user type");
	}
	
	
	public function testAddRoleToUserType()
    { 			
        $uri = '/institution/staff/user-roles/add-to-user-type';
        $params = array('userRoleId' => $this->userRole->getId(), 'userTypeId' => $this->userType->getId());
        // test that it will not accept a GET method
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri, $params);
        $this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Expecting method GET to be not accepted');
        
         // test valid post
         $client = $this->getBrowserWithActualLoggedInUser();
         $crawler = $client->request('POST', $uri, $params);
         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
         // test that adding same role to user type will throw an error 500
         $crawler = $client->request('POST', $uri, $params);
         $this->assertEquals(500, $client->getResponse()->getStatusCode(), "Expecting error 500 after adding same role to same user type");
          
        // test that adding invalid role to type will throw error 404 
        $crawler = $client->request('POST', $uri, array('userRoleId' => 123, 'userTypeId' => 123));
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "Expecting error 404 after adding invalid role to user type");
    }
	
    public function testRemoveRoleFromUserType()
    {
    	$uri = '/institution/staff/user-roles/remove-role-from-user-type';
    	$params = array('userRoleId' => $this->userRole->getId(), 'userTypeId' => $this->userType->getId());
    	
    	// test that it will not accept a GET method
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', $uri, $params);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Expecting method GET to be not accepted');
    	
    	// test to remove invalid
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('POST', $uri, array('userRoleId' => 99999, 'userTypeId' => 21312388324242399));
    	$this->assertEquals(404, $client->getResponse()->getStatusCode(), "Expecting error 404 after passing invalid user type and user role");
    	
    	// test valid data post
    	$crawler = $client->request('POST', $uri, $params);
    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	
    }
}

?>