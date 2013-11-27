<?php
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use Gaufrette\Adapter\file_exists;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

/**
 * Functional test for InstitutionController
 * @author Chaztine Blance
 * Set csrf token to true
 */
class InstitutionSignUpControllerTest extends InstitutionBundleWebTestCase
{
    private $signupFormValues = array(
        'institutionUserSignUp[firstName]' => 'testFirstName',
        'institutionUserSignUp[lastName]' => 'testLastName',
        'institutionUserSignUp[email]' => 'tests@aagfSaa.com', //make sure to change the email before running the test
        'institutionUserSignUp[confirm_email]' => 'tests@aagfSaa.com',
        'institutionUserSignUp[password]' => '123456',
        'institutionUserSignUp[confirm_password]' => '123456',
        'institutionUserSignUp[type]' => '1',
        'institutionUserSignUp[agree_to_terms]' => '1',
    );

    public function testSignUpWithInvalidFields()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/institution/register.html');
        $invalidValues = array(
            'institutionUserSignUp[password]' => null,
            'institutionUserSignUp[firstName]' => null,
            'institutionUserSignUp[lastName]' => null,
            'institutionUserSignUp[email]' => null,
            'institutionUserSignUp[confirm_email]' => null,
        );
    
        $form = $crawler->selectButton('Create Account')->form();
        $crawler = $client->submit($form, $invalidValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password is required.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your first name.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your last name.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your email address.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please choose at least one type of Institution")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("You must agree to the Terms of Use")')->count());
        
        $invalidValues['institutionUserSignUp']['password'] = '1234567';
        $invalidValues['institutionUserSignUp']['confirm_password'] = '7654321'; 
        $invalidValues['institutionUserSignUp']['email'] = 'test.adminuser@chromedia.com'; //existing email
        $invalidValues['institutionUserSignUp']['confirm_email'] = null;
        $crawler = $client->submit($form, $invalidValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Passwords do not match")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Email already exists.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Email address do not match")')->count());
        
        $invalidValues['institutionUserSignUp']['password'] = '123'; // password lenght validation should be 6
        $invalidValues['institutionUserSignUp']['email'] = 'invalidEmal'; //invalid email
        $crawler = $client->submit($form, $invalidValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide a valid email")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password is too short. Please enter at least 6 characters.")')->count());
        
    }
    
    public function testSignUpLinks()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/institution/register.html');
        $link = $crawler->filter('a:contains("Terms of Use")')->eq(1)->link();
        $crawler = $client->click($link);
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'no Terms of Use link redirected');

        $crawler = $client->request('GET', '/institution/register.html');
        $link = $crawler->filter('a:contains("Privacy Policy")')->eq(1)->link();
        $crawler = $client->click($link);
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'no Privacy Policy link redirected');
    }

	public function testSignUp()
	{
	    /* Test Register for multiple type */
		$client = static::createClient();
		$crawler = $client->request('GET', 'institution/register.html');
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your First Name *")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your Last Name *")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your Email address")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Set your password")')->count());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Re-type your password")')->count());
		
		$form = $crawler->selectButton('Create Account')->form();
		$crawler = $client->submit($form, $this->signupFormValues);
 		$this->assertEquals(302, $client->getResponse()->getStatusCode());
		$crawler = $client->followRedirect();
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Set-up Hospital Profile")')->count());
		/* END of Multitple Type Test Registration */
		
		/* Test Register for single type */
		$client = static::createClient();
		$crawler = $client->request('GET', 'institution/register.html');
		$form = $crawler->selectButton('Create Account')->form();
		$this->signupFormValues['institutionUserSignUp']['type'] = 3;
		$this->signupFormValues['institutionUserSignUp']['email'] = 'newSetEmail@singletype.com';
		$this->signupFormValues['institutionUserSignUp']['confirm_email'] = 'newSetEmail@singletype.com';
		
		$crawler = $client->submit($form, $this->signupFormValues);
		$this->assertEquals(302, $client->getResponse()->getStatusCode());
		$crawler = $client->followRedirect();
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Set-up Clinic Profile")')->count()); 

		/* END of Single Type Test Registration */
 	}

    private $setupProfileFormValues =  array( 'institution_profile_form' => array(
        'name' => 'new name',
        'address1' => array ( 'room_number' => 'test', 'building' => 'test', 'street' => 'test' ),
        'country' => '1',
        'state' => '1',
        'city' => '1',
        'description' => 'test',
        'zipCode' => '232',
        'contactEmail' => 'test@yahoo.com',
        'addressHint' => 'test', 
        'medicalProviderGroups' => array( '0' => 'group'),
        'coordinates' => '10.3112791,123.89776089999998',
        'socialMediaSites' => array ( 'facebook' => 'test', 'twitter' => 'test','googleplus' => 'test' ),
        'awards' => array( ),
        'services' => array( ),
        '_token' => '',
    ));

    public function testSetupProfileforMultipleType()
    {
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 1);
        $crawler = $client->request('GET', 'institution/setup-profile');
         
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Set-up Hospital Profile")')->count());
        $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        
        $invalidProfileFormValues['institution_profile_form']['name'] = null;
        $invalidProfileFormValues['institution_profile_form']['country'] = null;
        $invalidProfileFormValues['institution_profile_form']['city'] = null;
        $invalidProfileFormValues['institution_profile_form']['state'] = null;
        $invalidProfileFormValues['institution_profile_form']['zipCode'] = null;
        $invalidProfileFormValues['institution_profile_form']['medicalProviderGroups'] = array( '0' => '');
        $invalidProfileFormValues['institution_profile_form']['_token'] = $csrf_token;
        $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your hosipital name.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your country.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your city.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your postal code.")')->count());

        // Add Custom State and City
        $invalidProfileFormValues['institution_profile_form']['country'] = 1;
        $invalidProfileFormValues['custom_city'] = 'CustomCity1 withCustomState';
        $invalidProfileFormValues['custom_state'] = 'CustomsState withCustomCity1';
        $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);
        //var_dump($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Adding Custom State and City has failed!');
        
        // Add Custom City only
        $invalidProfileFormValues['institution_profile_form']['state'] = 1;
        $invalidProfileFormValues['custom_city'] = 'CustomCity2';
        $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Adding Custom City has failed!');
        
        $invalidProfileFormValues['institution_profile_form']['name'] = 'Apollo Hospital, Bangalore'; //existing institution name
        $invalidProfileFormValues['institution_profile_form']['contactEmail'] = 'invalidEmail';
        $invalidProfileFormValues['institution_profile_form']['address1'] = array ( 'room_number' => 'test', 'building' => 'test', 'street' => null );
        $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertGreaterThan(0, $crawler->filter('html:contains("This institution already exists!")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please supply a valid contact email.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide a valid address.")')->count());
        
        $invalidProfileFormValues['institution_profile_form']['country'] = 34543543; //invalid coutry value
        $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        
        $invalidProfileFormValues['institution_profile_form']['city'] = 1234556; //invalid city value
        $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        
        $this->setupProfileFormValues['institution_profile_form']['_token'] = $csrf_token;
        $crawler = $client->request('POST', '/institution/setup-profile', $this->setupProfileFormValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();        
    }
    
    public function testSetupProfileforSingleType()
    {
	    $client = $this->getBrowserWithActualLoggedInUserForSingleType();
	    $session = $client->getContainer()->get('session');
	    $session->set('institutionSignupStepStatus', 1);
	    //$session->set('accountId', 270);
	    $session->set('institutionId', 268);
	    $crawler = $client->request('GET', 'institution/setup-profile');
	    
	    $this->assertGreaterThan(0, $crawler->filter('html:contains("Set-up Clinic Profile")')->count());
	    $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
	    $csrf_token = $extract[0];
	    
	    $invalidProfileFormValues['institution_profile_form']['name'] = null;
	    $invalidProfileFormValues['institution_profile_form']['country'] = null;
	    $invalidProfileFormValues['institution_profile_form']['city'] = null;
	    $invalidProfileFormValues['institution_profile_form']['state'] = null;
	    $invalidProfileFormValues['institution_profile_form']['zipCode'] = null;
	    $invalidProfileFormValues['institution_profile_form']['medicalProviderGroups'] = array( '0' => '');
	    $invalidProfileFormValues['institution_profile_form']['_token'] = $csrf_token;
	    $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
	    $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your hosipital name.")')->count());
	    $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your hosipital name.")')->count());
	    $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your country.")')->count());
	    $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your city.")')->count());
	    $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your postal code.")')->count());
	    
	    $invalidProfileFormValues['institution_profile_form']['name'] = 'Apollo Hospital, Bangalore'; //existing institution name
	    $invalidProfileFormValues['institution_profile_form']['contactEmail'] = 'invalidEmail';
	    $invalidProfileFormValues['institution_profile_form']['address1'] = array ( 'room_number' => 'test', 'building' => 'test', 'street' => null );
	    $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);
	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
	    $this->assertGreaterThan(0, $crawler->filter('html:contains("This institution already exists!")')->count());
	    $this->assertGreaterThan(0, $crawler->filter('html:contains("Please supply a valid contact email.")')->count());
	    $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide a valid address.")')->count());
	    
	    $invalidProfileFormValues['institution_profile_form']['country'] = 34543543; //invalid coutry value
	    $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);
	    $this->assertEquals(500, $client->getResponse()->getStatusCode());
	    
	    $invalidProfileFormValues['institution_profile_form']['city'] = 1234556; //invalid city value
	    $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);
	    $this->assertEquals(500, $client->getResponse()->getStatusCode());
	    
	    $this->setupProfileFormValues['institution_profile_form']['name'] = 'dsfdsfdsfdssf';
	    $this->setupProfileFormValues['institution_profile_form']['_token'] = $csrf_token;

	    $crawler = $client->request('POST', '/institution/setup-profile', $this->setupProfileFormValues);
	    $this->assertEquals(302, $client->getResponse()->getStatusCode());

    }
    
    public function testUploadLogoForSetUpProfile()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
	    $crawler = $client->request('GET', 'institution/uploadLogo');
	    $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
	    $client = $this->getBrowserWithActualLoggedInUser();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 1);
        
        $filename = dirname($client->getContainer()->get('kernel')->getRootDir()) . '/web/images/flags16.png';
        $file['logo'] = new UploadedFile($filename, 'flags16.png', 'image/png', 63);
        $crawler = $client->request('POST', 'institution/uploadLogo', array('logoSize' => '100x100'), $file);
        $this->assertRegExp('/true/', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    
    public function testUploadCoverPhotoForSetUpProfile()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', 'institution/uploadFeaturedImage');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    
        $client = $this->getBrowserWithActualLoggedInUser();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 1);
    
        $filename = dirname($client->getContainer()->get('kernel')->getRootDir()) . '/web/images/default-hospital-featured-image.png';
        $file['featuredImage'] = new UploadedFile($filename, 'default-hospital-featured-image.png', 'image/png', 96);
        $crawler = $client->request('POST', 'institution/uploadFeaturedImage', array('logoSize' => '100x100'), $file);
        $this->assertRegExp('/true/', $client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testSetupInstitutionMedicalCenter()
    {
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 2);
        //$session->set('institutionId', 4);
        $crawler = $client->request('GET', 'institution/setup-clinic-details');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
	    $client = $this->getBrowserWithActualLoggedInUserForSingleType();
	    $crawler = $client->request('GET', 'institution/setup-clinic-details/2');
	    $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 2);
        $crawler = $client->request('GET', 'institution/setup-clinic-details/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institutionMedicalCenter[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        
        $invalidFormValues['institutionMedicalCenter[name]'] = null;
        $form = $crawler->selectButton('Continue to Adding Specializations')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Clinic name is required.")')->count());
        
        $invalidFormValues = array( 'institutionMedicalCenter' => array(
            'name' =>'Audiology Services', //existing name
            'description' => 'testing2',
            'contactEmail' => 'invalid email',
            'address' => array ( 'room_number' => null, 'building' => 'test', 'street' => null ), //invalid address
            '_token' => $csrf_token
        ),'isSameAddress' => 0);
        $form = $crawler->selectButton('Continue to Adding Specializations')->form();
        $form['isSameAddress']->select('0'); //select different address
        
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("We need you to correct some of your input. Please check the fields in red.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide a valid address.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please supply a valid contact email.")')->count());
        
        $formValues = array( 'institutionMedicalCenter' => array(
            'name' =>'new testdstsdf',
            'description' => 'testing2',
            'isAlwaysOpen' => 1,
            '_token' => $csrf_token
        ),'isSameAddress' => 1);
        $form = $crawler->selectButton('Continue to Adding Specializations')->form();
        $form['institutionMedicalCenter[isAlwaysOpen]']->select('1');
        $client->request('POST', "/institution/setup-clinic-details/1", $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 2);
        $crawler = $client->request('GET', 'institution/setup-clinic-details/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institutionMedicalCenter[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        
        $formValues = array( 'institutionMedicalCenter' => array(
            'name' =>'new testdstsdf',
            'description' => 'testing2',
            'contactEmail' => 'test@mail.com',
            'address' => array ( 'room_number' => null, 'building' => 'test', 'street' => 'test' ),
            'businessHours' =>  array('18a6a330-af4d-4371-a4e1-4ca50843847b' => '{"weekdayBitValue":16,"opening":"8:00 AM","closing":"5:00 PM","notes":""}'),
            '_token' => $csrf_token
        ));
        $client->request('POST', "/institution/setup-clinic-details/1", $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Specializations")')->count()); //check if redirects to add specializations
    }
    
    public function testSetupInstitutionMedicalCenterNoIdPassed()
    {
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 3);
        $crawler = $client->request('GET', 'institution/setup-clinic-details');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testSetupSpecializations()
    {
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 4);
        $crawler = $client->request('GET', 'institution/setup-specializations/1');
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Specializations")')->count());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $invalidProfileFormValues =  array( 'institutionSpecialization' => array( ));
        $crawler = $client->request('POST', '/institution/setup-specializations/1', $invalidProfileFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please select at least one specialization.")')->count());

        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $formVal['specializationId'] = 1;
        $crawler = $client->request('GET', 'ns-institution/1/ajax/load-specialization-treatments/1',$formVal);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        // Assert that the response content matches a regexp.
        $this->assertRegExp('/html/', $client->getResponse()->getContent());
        
        $formValues = array('institutionSpecialization' => array( 4 => array ('treatments' => array ( 0 => '5'))));
        $crawler = $client->request('POST', '/institution/setup-specializations/1', $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add New Doctor")')->count());
    }

    public function testSetupDoctors()
    {
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 5);
        
        $crawler1 = $crawler = $client->request('GET', 'institution/setup-doctors/1');
        $this->assertGreaterThan(0, $crawler1->filter('html:contains("Add New Doctor")')->count());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler1->filter('input[name="institutionMedicalCenterDoctor[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        
        $formValues =  array('institutionMedicalCenterDoctor' => array( //add doctor
                        'lastName' => 'lasts',
                        'firstName' => 'firsts',
                        'middleName' => 'middles',
                        'suffix' => 'Jr.',
                        '_token' => $csrf_token
        ));
        $client->request('POST', '/institution/setup-doctors/1', $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        /* Add invalid doctor */
        // test for invalid form
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $formDoctorValues =  array( 'institutionMedicalCenterDoctor' => array ('lastName' =>'chazzzi','firstName' => 'test', 'middleName' => '', 'suffix' =>  ''));
        $crawler = $client->request('POST', 'institution/setup-doctors/1', $formDoctorValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // Search for Doctor
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $crawler = $client->request('GET', 'search_doctors?criteria[lastName]=test&criteria[firstName]=test');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $formValues =  array( 'doctorId' => 43242); //add invalid doctor
        $crawler = $client->request('POST', 'institution/medical-center/1/add-existing-doctor', $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Add existing Doctor
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $formValues =  array( 'doctorId' => 1, '_token' => $csrf_token);
        $crawler = $client->request('POST', 'institution/medical-center/1/add-existing-doctor', $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUpdateDoctor(){
    
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $formDoctorValues = array( 'editInstitutionMedicalCenterDoctorForm' => 
                           array ('lastName' =>'chazzzi','firstName' => 'test', 'middleName' => '', 'suffix' =>  '','specializations' => array ( 0 => '1'), ));
        $crawler = $client->request('POST', 'institution/medical-center/1/update-doctor/1', $formDoctorValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testFinish(){
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 5);
        
        $crawler = $client->request('GET', 'institution/setup-finish');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
    }
    
}