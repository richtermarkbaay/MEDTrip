<?php
/**
 * Functional test for InstitutionController
 * 
 * @author Alnie Jacobe
 * @author Chaztine Blance
 *
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InstitutionSignUpControllerTest extends InstitutionBundleWebTestCase
{
    private $signupFormValues = array(
        'institutionSignUp[firstName]' => 'testFirstName',
        'institutionSignUp[lastName]' => 'testLastName',
        'institutionSignUp[email]' => 'test-email-watata-signup@chromedia.com',
        'institutionSignUp[password]' => '123456',
        'institutionSignUp[confirm_password]' => '123456',
        'institutionSignUp[type]' => '2',
        'institutionSignUp[agree_to_terms]' => '1',
    );
	
// 	public function testInviteInstitution()
// 	{
// 		$url = '/invite-institution';
	
// 		$client = static::createClient();
// 		$crawler = $client->request('GET', $url);
// 	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
// 		$this->assertGreaterThan(0, $crawler->filter('html:contains("Name")')->count()); // look for the Current name text
// 		$this->assertGreaterThan(0, $crawler->filter('html:contains("Email")')->count()); // look for the Current email text
	
// 		$formValues = array(
// 				'institutionInvitation[name]' => 'alnie jacobe',
// 				'institutionInvitation[email]' => 'test-sign-up-institutiont@chromedia.com',
// 		);
// 		$form = $crawler->selectButton('submit')->form();
// 		$crawler = $client->submit($form, $formValues);
// 		$this->assertEquals(200, $client->getResponse()->getStatusCode());
// 		$this->assertGreaterThan(0,$crawler->filter('html:contains("Invitation sent to test-sign-up-institutiont@chromedia.com")')->count());
	
// 		//test for missing email field
// 		$invalidValues = $formValues;
// 		$invalidValues['institutionInvitation[email]'] = null;
// 		$crawler = $client->submit($form, $invalidValues);
// 		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
	
// 		//test for missing name field
// 		$invalidValues = $formValues;
// 		$invalidValues['institutionInvitation[name]'] = null;
// 		$crawler = $client->submit($form, $invalidValues);
// 		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
	
// 	}

	public function testSignUp()
	{
	    
// 	    $client = $this->getBrowserWithActualLoggedInUser();
// 	    $crawler = $client->request('GET', 'institution/register.html');
// 	    $this->assertEquals(302, $client->getResponse()->getStatusCode());
	    
		$client = static::createClient();
		$crawler = $client->request('GET', 'institution/register.html');
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your First Name *")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your Last Name *")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your Email address")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Set your password")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Confirm your password")')->count());
		$form = $crawler->selectButton('Create Account')->form();
		$crawler = $client->submit($form, $this->signupFormValues);
		
		// test that it will redirect to institution homepage
 		$this->assertEquals(302, $client->getResponse()->getStatusCode());
		$client->followRedirect();
	}
	
	public function testSignUpWithInvalidFields() 
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/institution/register.html');
        $invalidValues = array(
                            'institutionSignUp[password]' => null,
                            'institutionSignUp[firstName]' => null,
                            'institutionSignUp[lastName]' => null,
                            'institutionSignUp[email]' => null,
                        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
         
        $form = $crawler->selectButton('Create Account')->form();
        $crawler = $client->submit($form, $invalidValues);
 
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password is required.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your first name.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your last name.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your email address.")')->count());
    }
    
    public function testSetupProfile()
    {
	    $client = $this->getBrowserWithActualLoggedInUserForSingleType();
	    $crawler = $client->request('GET', 'institution/setup-profile');
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
	    
	    $client = $this->getBrowserWithActualLoggedInUser();
	    $crawler = $client->request('GET', 'institution/setup-profile');
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
//     public function testSetupProfileSingleCenter()
//     {
// 	    $client = $this->getBrowserWithActualLoggedInUser();
// 	    $crawler = $client->request('GET', 'institution/register.html');
// 	    $this->assertEquals(302, $client->getResponse()->getStatusCode());
//     }
}