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

class institutionUserSignUpControllerTest extends InstitutionBundleWebTestCase
{
    private $signupFormValues = array(
        'institutionUserSignUp[firstName]' => 'testFirstName',
        'institutionUserSignUp[lastName]' => 'testLastName',
        'institutionUserSignUp[email]' => 'test_sisgnu1@chsromedia.com',
        'institutionUserSignUp[password]' => '123456',
        'institutionUserSignUp[confirm_password]' => '123456',
        'institutionUserSignUp[type]' => '1',
        'institutionUserSignUp[agree_to_terms]' => '1',
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
        $client = static::createClient();
        $crawler = $client->request('GET', '/institution/register.html');
        $invalidValues = array(
                        'institutionUserSignUp[password]' => null,
                        'institutionUserSignUp[firstName]' => null,
                        'institutionUserSignUp[lastName]' => null,
                        'institutionUserSignUp[email]' => null,
        );
    
        $form = $crawler->selectButton('Create Account')->form();
        $crawler = $client->submit($form, $invalidValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password is required.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your first name.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your last name.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your email address.")')->count());
    }

	public function testSignUp()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', 'institution/register.html');
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your First Name *")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your Last Name *")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your Email address")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Set your password")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Re-type your password")')->count());
		$form = $crawler->selectButton('Create Account')->form();
		$crawler = $client->submit($form, $this->signupFormValues);
		
		// test that it will redirect to institution homepagex
 		$this->assertEquals(302, $client->getResponse()->getStatusCode());
		$client->followRedirect();
	}
	
    public function testSetupProfileforSingleType()
    {
	    $client = $this->getBrowserWithActualLoggedInUserForSingleType();
	    $session = $client->getContainer()->get('session');
	    $session->set('institutionSignupStepStatus', 1);
	    
	    $crawler = $client->request('GET', 'institution/setup-profile');
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
	    $setupProfileFormValues = array(
            'institution_profile_form[name]' => 'test11221212121',
            'institution_profile_form[description]' => 'test',
            'institution_profile_form[address1]' => 'test',
            'institution_profile_form[country]' => '1',
            'institution_profile_form[city]' => '1',
            'institution_profile_form[zipCode]' => '2322',
            'institution_profile_form[addressHint]' => 'test.com',
            'institution_profile_form[contactEmail]' => '1',
            'institution_profile_form[contactNumber]' => '1',
            'institution_profile_form[websites]' => 'www.test.com',
            'institution_profile_form[socialMediaSites]' => 'test',
            'institution_profile_form[services]' => '1',
            'institution_profile_form[awards]' => '1',
            'institution_profile_form[coordinates]' => '12.879721, 121.77401699999996'
	    );
	    
	    $form = $crawler->selectButton('Confirm')->form();
	    $crawler = $client->submit($form, $this->signupFormValues);
    }
    
    public function testSetupProfileForMultitpleType()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', 'institution/setup-profile');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    
}