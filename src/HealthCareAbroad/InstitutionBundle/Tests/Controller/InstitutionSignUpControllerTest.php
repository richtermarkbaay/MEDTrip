<?php
/**
 * Functional test for InstitutionController
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InstitutionSignUpControllerTest extends InstitutionBundleWebTestCase
{
    private $signupFormValues = array(
        'institutionSignUp[name]' => 'this a test instiutiton adfasdfsaf',
        'institutionSignUp[email]' => 'test-email-watata-signup@chromedia.com',
        'institutionSignUp[password]' => '123456',
        'institutionSignUp[confirm_password]' => '123456',
        'institutionSignUp[agree_to_terms]' => '1',
    );
	
	public function testInviteInstitution()
	{
		$url = '/invite-institution';
	
		$client = static::createClient();
		$crawler = $client->request('GET', $url);
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Email")')->count()); // look for the Current email text
	
		$formValues = array(
				'institutionInvitation[name]' => 'alnie jacobe',
				'institutionInvitation[email]' => 'test-sign-up-institutiont@chromedia.com',
		);
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $formValues);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0,$crawler->filter('html:contains("Invitation sent to test-sign-up-institutiont@chromedia.com")')->count());
	
		//test for missing email field
		$invalidValues = $formValues;
		$invalidValues['institutionInvitation[email]'] = null;
		$crawler = $client->submit($form, $invalidValues);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
	
		//test for missing name field
		$invalidValues = $formValues;
		$invalidValues['institutionInvitation[name]'] = null;
		$crawler = $client->submit($form, $invalidValues);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
	
	}

    public function testSignUpWithInvalidFields()
    {
        $invalidValues = array(
            'institutionSignUp[name]' => '',
            'institutionSignUp[email]' => '',
            'institutionSignUp[password]' => '',
            'institutionSignUp[confirm_password]' => '',
        );
        
        $client = static::createClient();
        $crawler = $client->request('GET', '/sign-up');
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $client->submit($form, $invalidValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->assertGreaterThan(0, $crawler->filter('html:contains("You must agree to the terms and conditions")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Institution name is required")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide a valid email")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password is required")')->count());
        
        // test not matching passwords
        $invalidValues['institutionSignUp[password]'] = '654321';
        $invalidValues['institutionSignUp[confirm_password]'] = '123456';
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $client->submit($form, $invalidValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Passwords do not match")')->count());
        
        // test existing email
        $invalidValues['institutionSignUp[email]'] = 'test.institutionuser@chromedia.com';
        $form = $crawler->selectButton('Submit')->form();
        $crawler = $client->submit($form, $invalidValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Email already exists")')->count());
    }
	
	public function testSignUp()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/sign-up');
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Name of the Institution")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("email")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("password")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("re-type password")')->count());
		$form = $crawler->selectButton('Submit')->form();
		$crawler = $client->submit($form, $this->signupFormValues);
		
		// test that it will redirect to institution homepage
		$this->assertEquals(302, $client->getResponse()->getStatusCode());
		$this->assertEquals('/institution', $this->getLocationResponseHeader($client));
		$client->followRedirect();
		
		// test that institution homepage has ok status
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}
}