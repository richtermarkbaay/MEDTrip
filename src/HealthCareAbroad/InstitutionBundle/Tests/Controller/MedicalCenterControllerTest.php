<?php 

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

use Symfony\Component\HttpFoundation\Session\Session;

use \HCA_DatabaseManager;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class MedicalCenterControllerTest extends InstitutionBundleWebTestCase
{
	public function testIndex()
	{
	    $uri = '/institution/medical-centers';
	    
	    // test for no login user
	    $client = $this->requestUrlWithNoLoggedInUser($uri);
	    $this->assertEquals(302, $client->getResponse()->getStatusCode());
	    $this->assertTrue($this->isRedirectedToLoginPage($client), 'Expecting redirec to login page');
	    
	    // test for correct logged user
	    $client = $this->getBrowserWithActualLoggedInUser();
	    $crawler = $client->request('GET', $uri);
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
	    $this->assertGreaterThan(0,$crawler->filter('title:contains("Institution Panel Medical Centers")')->count());
	    
	    // test with invalid institution id
	    $this->setInvalidInstitutionInSession($client);
	    $crawler = $client->request('GET', $uri);
	    $this->assertEquals(404, $client->getResponse()->getStatusCode());
	}	
}