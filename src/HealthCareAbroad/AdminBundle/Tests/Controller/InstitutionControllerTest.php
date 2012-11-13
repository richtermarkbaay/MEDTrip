<?php
/**
 * Functional test for Admin InstitutionController
 *
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InstitutionControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institutions');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('#page-heading > h3')->text() == 'List of Institutions', 'No Output!');
    }
    
    public function testView()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/view');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testViewInvalidInstitution()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/10010/view');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testUpdateStatus()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $status = InstitutionStatus::ACTIVE;
        $crawler = $client->request('POST', '/admin/institution/1/update-status', array('status' => $status));

        $response = $client->getResponse();

        // check of redirect url /admin/institutions
        $this->assertEquals('/admin/institutions', $client->getResponse()->headers->get('location'));
        $this->assertEquals(302, $response->getStatusCode());

        $crawler = $client->followRedirect(true);

        $isValidStatus = $crawler->filter('#message-red')->count() == 0;
        $this->assertTrue($isValidStatus, 'Invalid status value ' . $status);

//         $isStatusUpdated = $crawler->filter('#message-green')->count() > 0;
//         $this->assertTrue($isStatusUpdated, 'Unable to update status.');
    }
    
    public function testUpdateInvalidStatus()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $invalidStatus = 35;
        $crawler = $client->request('POST', '/admin/institution/1/update-status', array('status' => $invalidStatus));

        $response = $client->getResponse();
        
        $this->assertEquals('/admin/institutions', $response->headers->get('location'));
        $this->assertEquals(302, $response->getStatusCode());

        $crawler = $client->followRedirect(true);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

//         $isNotValidStatus = $crawler->filter('#message-red')->count() > 0;
//         $this->assertTrue($isNotValidStatus, 'Invalid status value should not be saved!');
    }
    
    private $signupFormValues = array(
    					'institutionSignUp[name]' => 'Institution Test',
    					'institutionSignUp[email]' => 'testsignup@chromedia.com',
    					'institutionSignUp[password]' => '123456',
    					'institutionSignUp[confirm_password]' => '123456',
    					'institutionType' => '1'
    	);
    
	public function testAddWithInvalidFields()
	{
		$invalidValues = array(
			'institutionSignUp[name]' => '',
			'institutionSignUp[email]' => '',
			'institutionSignUp[password]' => '',
			'institutionSignUp[confirm_password]' => '',
		);
    
    		$client = $this->getBrowserWithActualLoggedInUser();
    		$crawler = $client->request('GET','/admin/institution/add');
    		$form = $crawler->selectButton('Next')->form();
    		$crawler = $client->submit($form, $invalidValues);
    		$this->assertEquals(200, $client->getResponse()->getStatusCode());
    
    		$this->assertGreaterThan(0, $crawler->filter('html:contains("Institution name is required")')->count());
    		$this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide a valid email")')->count());
    		$this->assertGreaterThan(0, $crawler->filter('html:contains("Password is required")')->count());
    
    		// test not matching passwords
    		$invalidValues['institutionSignUp[password]'] = '654321';
    		$invalidValues['institutionSignUp[confirm_password]'] = '123456';
    		$form = $crawler->selectButton('Next')->first()->form();
    		$crawler = $client->submit($form, $invalidValues);
    		$this->assertGreaterThan(0, $crawler->filter('html:contains("Passwords do not match")')->count());
    
    		// test existing email
    		$invalidValues['institutionSignUp[email]'] = 'test.institutionuser@chromedia.com';
    		$form = $crawler->selectButton('Next')->first()->form();
    		$crawler = $client->submit($form, $invalidValues);
    		$this->assertGreaterThan(0, $crawler->filter('html:contains("Email already exists")')->count());
   	}
    
    public function testAdd()
    {
    
    		$client = $this->getBrowserWithActualLoggedInUser();
    		$crawler = $client->request('GET', '/admin/institution/add');
    
    		$this->assertEquals(200, $client->getResponse()->getStatusCode());
    		$this->assertGreaterThan(0, $crawler->filter('html:contains("Name of the Institution")')->count());
    		$this->assertGreaterThan(0, $crawler->filter('html:contains("Email")')->count());
    		$this->assertGreaterThan(0, $crawler->filter('html:contains("Password")')->count());
    		$this->assertGreaterThan(0, $crawler->filter('html:contains("Re-type password")')->count());
    
    		$form = $crawler->selectButton('Next')->first()->form();
    		$crawler = $client->submit($form, $this->signupFormValues);
    					
    }
    
    public function testAddDetails()
	{
		$editAccountUrl = '/admin/institution/add-details?institutionId=1';
    
		//---- test that this should not be accessed by anonymous user
		$client = $this->requestUrlWithNoLoggedInUser($editAccountUrl);
		$this->assertEquals(302, $client->getResponse()->getStatusCode());
		$redirectLocation = $client->getResponse()->headers->get('location');
		$this->assertTrue($redirectLocation=='http://localhost/admin/login');

		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', $editAccountUrl);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		
		$formValues = array(
						'institutionDetail[description]' => 'TEST',
						'institutionDetail[contactEmail]' => 'tetmail2@fdfewed.com',
						'institutionDetail[country]' => '1',
						'institutionDetail[city]' => '1',
						'institutionDetail[address1]' => '3434',
						'institutionDetail[state]' => '34',
						'institutionDetail[zipCode]' => '3434'
		);
		
		//test for invalid description
		$invalidFormValues = $formValues;
		$invalidFormValues['institutionDetail[description]'] = null;
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
		
		//test valid values
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', $editAccountUrl);
		$crawler = $client->submit($form, $formValues);
	}
    
}