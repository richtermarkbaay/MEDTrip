<?php
/**
 * Functional test for InstitutionUserController
 * 
 * @author Allejo Chris G. Velarde
 * @author Chaztine Blance
 * 
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;
use Symfony\Component\HttpFoundation\Request;

class InstitutionUserControllerTest extends InstitutionBundleWebTestCase
{
    
    /**
     * Functional test for login and logout flow
     */
//     public function testInvalidLoginFlow()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', '/institution/login');
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         $client = static::createClient();
//         $crawler = $client->request('GET', '/institution/login');
        
//         $invalidFormValues = array( '_username' => 'invalid@email.com', // case sensitive for the input e.g. name="_username"
//                    '_password' => '09322' // case sensitive for the input e.g. name="_password"
//             );
//         $form = $crawler->selectButton('Login')->form();
//         $crawler = $client->submit($form, $invalidFormValues);
//         $this->assertTrue($client->getResponse()->isRedirect(), 'The email address or password you entered is incorrect.');
        
//     }
    
//     public function testEditAccountPassword()
//     {
//         $editAccountPassUrl = '/institution/manage-account-password.html';
        
//         //---- test that this should not be accessed by anonymous user
//         $client = $this->requestUrlWithNoLoggedInUser($editAccountPassUrl);
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
//         $redirectLocation = $client->getResponse()->headers->get('location');
//         $this->assertTrue($redirectLocation=='/institution/location' || $redirectLocation == 'http://localhost/institution/login');

//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', $editAccountPassUrl);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Manage Account Password")')->count());

//         $formValues =  array( 'institutionUserChangePasswordType' => array(
//                             'current_password' => $this->userPassword,
//                             'new_password' => $this->userPassword .'1',
//                             'confirm_password' => $this->userPassword .'1',
//                             )
//                     );
//         $form = $crawler->selectButton('Save Changes')->form();
//         $crawler = $client->submit($form, $formValues);
//         \HCA_DatabaseManager::getInstance()->restoreGlobalAccountsDatabaseState();
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         //test empty fields
//         $invalidFormValues =  array( 'institutionUserChangePasswordType' => array(
//                         'current_password' => null,
//                         'new_password' => null,
//                         'confirm_password' => null,
//             )
//         );
        
//         $form = $crawler->selectButton('Save Changes')->form();
//         $crawler = $client->submit($form, $invalidFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Incorrect password.")')->count());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Password is required.")')->count());
        
//         //test incorrect current password
//         $invalidFormValues =  array( 'institutionUserChangePasswordType' => array(
//                         'current_password' => '123456',
//                         'new_password' => '1234567',
//                         'confirm_password' => '7654321')
//         );
        
//         $form = $crawler->selectButton('Save Changes')->form();
//         $crawler = $client->submit($form, $invalidFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Passwords do not match")')->count());
        
//         //test short entered value
//         $invalidFormValues =  array( 'institutionUserChangePasswordType' => array(
//                         'current_password' => '123',
//                         'new_password' => '123',
//                         'confirm_password' => '123')
//         );
        
//         $form = $crawler->selectButton('Save Changes')->form();
//         $crawler = $client->submit($form, $invalidFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Incorrect password.")')->count());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Password is too short. Please enter at least 6 characters.")')->count());

//         $client = $this->getBrowserWithActualLoggedInUser();
//         $session = $client->getContainer()->get('session');
//         $session->set('accountId', 234);
//         $crawler = $client->request('GET', $editAccountPassUrl);
//         $this->assertEquals(403, $client->getResponse()->getStatusCode());
//     }
    
//     public function testEditAccount()
//     {
//         $editAccountUrl = '/institution/manage-account-profile.html';
        
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', $editAccountUrl);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Manage Account Profile")')->count());
        
//         $formValues = array(
//             'institutionUserSignUp[firstName]' => 'tset resr',
//             'institutionUserSignUp[contactDetails]' => array ( '0' =>  array ( 'country' => '1', 'area_code' => '343','number' => '434','ext' => '3' ),
//                                                 '1' =>  array ( 'country' => '1', 'area_code' => '343','number' => '434')),
//             'institutionUserSignUp[lastName]' => 'rsersr',
//             'institutionUserSignUp[jobTitle]' => 'tester'
//         );
//         $form = $crawler->selectButton('Save Changes')->form();
//         $crawler = $client->submit($form, $formValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Success! Updated Account');
        
        
//         $invalidFormValues = array(
//             'institutionUserSignUp[firstName]' => null,
//             'institutionUserSignUp[lastName]' => null,
//         );

//         $form = $crawler->selectButton('Save Changes')->form();
//         $crawler = $client->submit($form, $invalidFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your first name. ")')->count());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide your last name.")')->count());
        
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $session = $client->getContainer()->get('session');
//         $session->set('accountId', 234);
//         $crawler = $client->request('GET', $editAccountUrl);
//         $this->assertEquals(403, $client->getResponse()->getStatusCode());
//     }

//     public function testChangeEmailAddress()
//     {
//         $uri = '/institution/manage-account-email.html';
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', $uri);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Manage Account Email")')->count());
        
//         $formValues = array(
//             'institutionUserChangeEmail[new_email]' => 'test@new.mail.com',
//             'institutionUserChangeEmail[confirm_email]' => 'test@new.mail.com',
//         );
//         $form = $crawler->selectButton('Save Changes')->form();
//         $crawler = $client->submit($form, $formValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         //test invalid email
//         $invalidFormValues = array(
//             'institutionUserChangeEmail[new_email]' => 'test',
//             'institutionUserChangeEmail[confirm_email]' => 'test@new.com',
//         );
//         $form = $crawler->selectButton('Save Changes')->form();
//         $crawler = $client->submit($form, $invalidFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide a valid email")')->count());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Email address do not match")')->count());
        
//         // test existing email
//         $invalidFormValues = array( 'institutionUserChangeEmail[new_email]' => 'test.adminuser@chromedia.com');
//         $form = $crawler->selectButton('Save Changes')->form();
//         $crawler = $client->submit($form, $invalidFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Email address already exists.")')->count());
        
//         // test empty email
//         $invalidFormValues = array('institutionUserChangeEmail[new_email]' => null );
//         $form = $crawler->selectButton('Save Changes')->form();
//         $crawler = $client->submit($form, $invalidFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Email address is required.")')->count());
        
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $session = $client->getContainer()->get('session');
//         $session->set('accountId', 234);
//         $crawler = $client->request('GET', $uri);
//         $this->assertEquals(403, $client->getResponse()->getStatusCode());
//     }
    
//     public function testResetPassword()
//     {
//         $uri = '/institution/reset.html';
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', $uri);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Reset Password ")')->count());
        
//         $formValues = array('email' => 'test.adminuser@chromedia.com');
//         $form = $crawler->selectButton('Reset Password')->form();
//         $crawler = $client->submit($form, $formValues);
//         $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'Success! Updated Account');
//         $crawler = $client->followRedirect();
        
//         $invalidFormValues = array('email' => 'invalid@email.com');
//         $crawler = $client->submit($form, $invalidFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid Email');
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("The email address you entered does not exist. Please check and try again.")')->count());
//     }
    
    public function testChangePassword()
    {
        $uri = '/institution/set-new-password';
        
        $client = $this->getBrowserWithActualLoggedInUserForSingleType();
        $crawler = $client->request('GET', $uri.'/a2846338db37bb3cca03211ceb8910822a5bb7862c02efb211dce7859b426036'); //no expired
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri.'/25883977e3635cf8cc47bfeb8d822e4aeff213fb3f34d6b427278542a7db32f1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Create New Password")')->count());
        
        $invalidFormValues =  array( 'institutionUserResetPasswordType' => array(
                        'new_password' => null,
                        'confirm_password' => '7654321')
        );
        $form = $crawler->selectButton('Reset Password')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Passwords do not match")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count());
        
        $formValues = array(
            'institutionUserResetPasswordType[new_password]' => '12345678',
            'institutionUserResetPasswordType[confirm_password]' => '12345678',
        );
        $form = $crawler->selectButton('Reset Password')->form();
        $crawler = $client->submit($form, $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'Success! Updated Account');
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri.'/25883977e3635cf8cc47bfeb8d822e4aeff213fb3f34d6b427278542a7db32f6'); //invalid account
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
    }
}