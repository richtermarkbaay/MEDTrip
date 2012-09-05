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
		$this->userRole = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserRole')->find(1);
	
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
		
	}
	
	
	public function testAddAndRemoveRoleToUserType()
    { 			
        $uri = '/institution/staff/user-roles/add-to-user-type';
        $params = array('userRoleId' => $this->userRole->getId(), 'userTypeId' => $this->userType->getId());

        // test that it will not accept a GET method
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', $uri, $params);
        $this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Expecting method GET to be not accepted');
        
        // test valid post
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', $uri, $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
	
}

?>