<?php
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

/**
 * Functional test for InstitutionController
 * @author Chaztine Blance
 * Set csrf token to true
 */
class InstitutionSignUpControllerTest extends InstitutionBundleWebTestCase
{
//     private $signupFormValues = array(
//         'institutionUserSignUp[firstName]' => 'testFirstName',
//         'institutionUserSignUp[lastName]' => 'testLastName',
//         'institutionUserSignUp[email]' => 'testsisde@trassa.com', //make sure to change the email before running the test
//         'institutionUserSignUp[password]' => '123456',
//         'institutionUserSignUp[confirm_password]' => '123456',
//         'institutionUserSignUp[type]' => '1',
//         'institutionUserSignUp[agree_to_terms]' => '1',
//     );
	
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

//     public function testSignUpWithInvalidFields()
//     {
//         $client = static::createClient();
//         $crawler = $client->request('GET', '/institution/register.html');
//         $invalidValues = array(
//                         'institutionUserSignUp[password]' => null,
//                         'institutionUserSignUp[firstName]' => null,
//                         'institutionUserSignUp[lastName]' => null,
//                         'institutionUserSignUp[email]' => null,
//         );
    
//         $form = $crawler->selectButton('Create Account')->form();
//         $crawler = $client->submit($form, $invalidValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Password is required.")')->count());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your first name.")')->count());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your last name.")')->count());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your email address.")')->count());
//     }

// 	public function testSignUp()
// 	{
// 		$client = static::createClient();
// 		$crawler = $client->request('GET', 'institution/register.html');
// 		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your First Name *")')->count());
// 		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your Last Name *")')->count());
// 		$this->assertGreaterThan(0, $crawler->filter('html:contains("Your Email address")')->count());
// 		$this->assertGreaterThan(0, $crawler->filter('html:contains("Set your password")')->count());
// 		$this->assertGreaterThan(0, $crawler->filter('html:contains("Re-type your password")')->count());
		
// 		$form = $crawler->selectButton('Create Account')->form();
// 		$crawler = $client->submit($form, $this->signupFormValues);
// 		// test that it will redirect to institution homepagex
//  		$this->assertEquals(302, $client->getResponse()->getStatusCode());
// 		$client->followRedirect();
// 	}

    private $setupProfileFormValues =  array( 'institution_profile_form' => array(
        'name' => 'new name',
        'address1' => array ( 'room_number' => 'test', 'building' => 'test', 'street' => 'test' ),
        'country' => '1',
        'city' => '1',
        'description' => 'test',
        'state' => '1',
        'zipCode' => '232',
        'contactEmail' => 'test@yahoo.com',
        'addressHint' => 'test', 
        'medicalProviderGroups' => array( '0' => 'group'),
        'coordinates' => '10.3112791,123.89776089999998',
        'socialMediaSites' => array ( 'facebook' => 'test', 'twitter' => 'test','googleplus' => 'test' ),
        '_token' => '',
    ));

    public function testSetupProfileforMultipleType()
    {
        
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 1);
        $invalidProfileFormValues['institution_profile_form']['name'] = null;
        $invalidProfileFormValues['institution_profile_form']['country'] = null;
        $invalidProfileFormValues['institution_profile_form']['city'] = null;
        $invalidProfileFormValues['institution_profile_form']['zipCode'] = null;
        $invalidProfileFormValues['institution_profile_form']['medicalProviderGroups'] = array( '0' => '');
        $crawler = $client->request('POST', '/institution/setup-profile', $invalidProfileFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your hosipital name.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your country.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your city.")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your postal code.")')->count());
        
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $session = $client->getContainer()->get('session');
        $session->set('institutionSignupStepStatus', 1);
        $crawler = $client->request('GET', 'institution/setup-profile');
         
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Set-up Hospital Profile")')->count());
        $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        $this->setupProfileFormValues['institution_profile_form']['name'] = 'nwewew';
        $this->setupProfileFormValues['institution_profile_form']['_token'] = $csrf_token;
         
        $crawler = $client->request('POST', '/institution/setup-profile', $this->setupProfileFormValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        
    }
    

//     public function testSetupProfileforSingleType()
//     {
// 	    $client = $this->getBrowserWithActualLoggedInUserForSingleType();
// 	    $session = $client->getContainer()->get('session');
// 	    $session->set('institutionSignupStepStatus', 1);
// 	    $crawler = $client->request('GET', 'institution/setup-profile');
	    
// 	    $this->assertGreaterThan(0, $crawler->filter('html:contains("Set-up Clinic Profile")')->count());
// 	    $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
// 	    $csrf_token = $extract[0];
// 	    $this->setupProfileFormValues['institution_profile_form']['_token'] = $csrf_token;
	    
// 	    $crawler = $client->request('POST', '/institution/setup-profile', $this->setupProfileFormValues);
// 	    $this->assertEquals(302, $client->getResponse()->getStatusCode());
	    
// 	    $client = $this->getBrowserWithActualLoggedInUserForSingleType();
// 	    $this->setupProfileFormValues['institution_profile_form']['name'] = null;
//     $this->setupProfileFormValues['institution_profile_form']['country'] = null;
//     $this->setupProfileFormValues['institution_profile_form']['city'] = null;
//     $this->setupProfileFormValues['institution_profile_form']['zipCode'] = null;
// 	    $this->setupProfileFormValues['institution_profile_form']['medicalProviderGroups'] = array( '0' => '');
// 	    $crawler = $client->request('POST', '/institution/setup-profile', $this->setupProfileFormValues);
// 	    $this->assertEquals(200, $client->getResponse()->getStatusCode());
// 	    $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your hosipital name.")')->count());
//     $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your hosipital name.")')->count());
//     $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your country.")')->count());
//     $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your city.")')->count());
//     $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your postal code.")')->count());
//     }
    
//     public function testSetupInstitutionMedicalCenter()
//     {
// 	    $client = $this->getBrowserWithActualLoggedInUserForSingleType();
// 	    $crawler = $client->request('GET', 'institution/setup-clinic-details/2');
// 	    $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
//         $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
//         $session = $client->getContainer()->get('session');
//         $session->set('institutionSignupStepStatus', 2);
//         $crawler = $client->request('GET', 'institution/setup-clinic-details/1');
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         $formValues = array(
//                         'institutionMedicalCenter[name]' => 'testFirstName',
//                         'institutionMedicalCenter[description]' => 'testLastName',
//                         'institutionMedicalCenter[city]' => '1',
//                         'institutionMedicalCenter[zipCode]' => '3423',
//                         'institutionMedicalCenter[contactEmail]' => '123456@mail.com',
//                         'institutionMedicalCenter[address]' => array ( 'room_number' => 'test', 'building' => 'test', 'street' => 'test' ),
//                         'institutionMedicalCenter[socialMediaSites]' => array ( 'facebook' => 'tttt' , 'twitter' => 'dsdf', 'googleplus' =>'')
//         );
        
//         $form = $crawler->selectButton('Continue to Adding Specializations')->form();
//         $crawler = $client->submit($form, $formValues);
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
//         $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
// 	    $formValues['institutionMedicalCenter[name]'] = null;
// 	    $crawler = $client->submit($form, $formValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//     }
    
//     public function testSetupInstitutionMedicalCenterNoIdPassed()
//     {
    
//         $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
//         $session = $client->getContainer()->get('session');
//         $session->set('institutionSignupStepStatus', 3);
//         $crawler = $client->request('GET', 'institution/setup-clinic-details');
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }
    
    
//     public function testSetupSpecializations()
//     {
    
//         $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
//         $session = $client->getContainer()->get('session');
//         $session->set('institutionSignupStepStatus', 4);
//         $crawler = $client->request('GET', 'institution/setup-specializations/1');
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Specializations")')->count());
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         $crawler = $client->request('GET', 'ns-institution/1/ajax/load-specialization-treatments/4');
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
//         $invalidProfileFormValues =  array( 'institutionSpecialization' => array( ));
//         $crawler = $client->request('POST', '/institution/setup-specializations/1', $invalidProfileFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Please select at least one specialization.")')->count());
        
//         $formValues = array('institutionSpecialization' => array( 4 => array ('treatments' => array ( 0 => '5'))));
//         $crawler = $client->request('POST', '/institution/setup-specializations/1', $formValues);
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
//         $client->followRedirect();
//     }

//     public function testSetupDoctors()
//     {
//         $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
//         $session = $client->getContainer()->get('session');
//         $session->set('institutionSignupStepStatus', 5);
        
//         $crawler = $client->request('GET', 'institution/setup-doctors/1');
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Add New Doctor")')->count());
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());

//         // Search for Doctor
//         $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
//         $crawler = $client->request('GET', 'search_doctors?criteria[lastName]=test&criteria[firstName]=test');
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
            
//              /* NOTE: this test only works if csrf token is set to fasle */
//              /*   Add new doctor */
// //                 $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
// //                 $formDoctorValues =  array( 'institutionMedicalCenterDoctor' => array ('lastName' =>'chazzzi','firstName' => 'test', 'middleName' => '', 'suffix' =>  ''));
// //                 $crawler = $client->request('POST', 'institution/setup-doctors/1', $formDoctorValues);
// //                 $this->assertEquals(200, $client->getResponse()->getStatusCode());
//              /* end of NOTE */
        
//         $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
//         $formValues =  array( 'doctorId' => 43242); //add invalid doctor
//         $crawler = $client->request('POST', 'institution/medical-center/1/add-existing-doctor', $formValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         //Add existing Doctor
//         $formValues =  array( 'doctorId' => 1);
//         $crawler = $client->request('POST', 'institution/medical-center/1/add-existing-doctor', $formValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }

//     /* NOTE: this test only works if csrf token is set to fasle */
// //     public function testUpdateDoctor(){
    
// //         $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
// //         $formDoctorValues = array( 'editInstitutionMedicalCenterDoctorForm' => 
// //                            array ('lastName' =>'chazzzi','firstName' => 'test', 'middleName' => '', 'suffix' =>  '','specializations' => array ( 0 => '1'),
// //                                    ));
// //         $crawler = $client->request('POST', 'institution/medical-center/1/update-doctor/1', $formDoctorValues);
// //         $this->assertEquals(200, $client->getResponse()->getStatusCode());
// //     }
//     /* end of NOTE */
    
//     public function testFinish(){
//         $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
//         $session = $client->getContainer()->get('session');
//         $session->set('institutionSignupStepStatus', 5);
        
//         $crawler = $client->request('GET', 'institution/setup-finish');
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
//         $client->followRedirect();
//     }
    
}