<?php
/**
 * Functional test for InstitutionUserController
 * 
 * @author Allejo Chris G. Velarde
 * 
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;
use Symfony\Component\HttpFoundation\Request;

class InstitutionUserControllerTest extends InstitutionBundleWebTestCase
{
    
//     public function testResetPasswordAction()
//     {
//         $resetUrl = '/institution/reset.html';
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', $resetUrl);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
    
//         $formValues =  array( 'email' => 'test.user@chromedia.com');
//         $crawler = $client->request('POST', $resetUrl, $formValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
    
//         ///test validate token
//         $validateTokenUrl = '/institution/set-new-password/25883977e3635cf8cc47bfeb8d822e4aeff213fb3f34d6b427278542a7db32f1';
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', $validateTokenUrl);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
    
//         $formValues =  array( 'institutionUserResetPasswordType' => array(
//                         'new_password' => 'sad',
//                         'confirm_password' => 'sad')
//         );
//         $crawler = $client->request('POST', $validateTokenUrl, $formValues);
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
    
//     }
    
    /**
     * Functional test for login and logout flow
     */
    public function testLoginFlow()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/institution/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $client = static::createClient();
        $crawler = $client->request('GET', '/institution/login');
        
        $form = $crawler->selectButton('Login')->form();
        $crawler = $client->submit(
            $form,
                array( '_username' => 'chrunchy@test.com', // case sensitive for the input e.g. name="_username"
                       '_password' => '1234567' // case sensitive for the input e.g. name="_password"
                )
        );
//         $this->assertTrue($client->getResponse()->isRedirect());
//         $crawler = $client->followRedirect();
        $this->assertTrue($client->getResponse()->isRedirect(), 'The email address or password you entered is incorrect.');
        
    }
    
    
//     public function testInviteFlow()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', '/institution/invite-staff');
        
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         $formValues = array(
//             'institutionUserInvitation[firstName]' => 'AAA',
//             'institutionUserInvitation[middleName]' => 'BBB',
//             'institutionUserInvitation[lastName]' => 'CCC',
//             'institutionUserInvitation[firstName]' => 'AAA',
//             'institutionUserInvitation[email]' => 'aaatest@chromedia.com',
//             'institutionUserInvitation[message]' => 'this is the message',
//         );
//         $form = $crawler->selectButton('submit')->form();
//         $crawler = $client->submit($form, $formValues);
        
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
//         $this->assertEquals('/institution/staff', $client->getResponse()->headers->get('location'));
        
//         // test for missing fields flow
//         $crawler = $client->request('GET', '/institution/invite-staff');
//         $formValues = array(
//             'institutionUserInvitation[firstName]' => 'AAA',
//             'institutionUserInvitation[middleName]' => 'BBB',
//             'institutionUserInvitation[lastName]' => 'CCC',
//             'institutionUserInvitation[firstName]' => 'AAA',
//             'institutionUserInvitation[email]' => '',
//             'institutionUserInvitation[message]' => 'this is the message',
//         );
//         $form = $crawler->selectButton('submit')->form();
//         $crawler = $client->submit($form, $formValues);
        
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
//     }
    
    
//     public function testAcceptInvitation()
//     {
//         $client = static::createClient();
//         $uri = '/accounts/accept-invitation/94f348d1f65c54cae854b22e5fcc949b408da4682efd9567a66fdbe8323595b7';
//         $crawler = $client->request('GET', $uri);

//         $this->assertEquals(302, $client->getResponse()->getStatusCode()); // test that it has been redirected to homepage
//         $this->assertEquals('/institution', $client->getResponse()->headers->get('location'));
//     }
    
//     /**
//      * @depends testAcceptInvitation
//      */
//     public function testAcceptInvalidInvitation()
//     {
//         $client = static::createClient();
//         $uri = '/accounts/accept-invitation/94f348d1f65c54cae854b22e5fcc949b408da4682efd9567a66fdbe8323595b7';
//         $crawler = $client->request('GET', $uri);
        
//         // test that it should be a 404 error code since this token has already been accepted
//         $this->assertEquals(404, $client->getResponse()->getStatusCode());
//     }
    
//     public function testEditAccount()
//     {
//         $editAccountUrl = '/institution/edit-account';
//         //---- test that this should not be accessed by anonymous user
//         $client = $this->requestUrlWithNoLoggedInUser($editAccountUrl);
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
//         $redirectLocation = $client->getResponse()->headers->get('location');
//         $this->assertTrue($redirectLocation=='/institution/location' || $redirectLocation == 'http://localhost/institution/login');
        
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', $editAccountUrl);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Login Information")')->count()); 
        
//         $client = $this->getBrowserWithActualLoggedInUser();
        
//         $formValues =  array( 'institutionUserChangePasswordType' => array(
//                 'current_password' => $this->userPassword,
//                 'new_password' => $this->userPassword .'1',
//                 'confirm_password' => $this->userPassword .'1',
//                 )
//         );
        
//         $invalidFormValues =  array( 'institutionUserChangePasswordType' => array(
//                 'current_password' => null,
//                 'new_password' => null,
//                 'confirm_password' => null,
//             )
//         );
        
//         $crawler = $client->request('POST', $editAccountUrl, $formValues);
//         \HCA_DatabaseManager::getInstance()->restoreGlobalAccountsDatabaseState();
//         $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Success! Updated Password');
        
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('POST', $editAccountUrl, $invalidFormValues);
//         $this->assertTrue($crawler->filter('Failed')->count() == 0, 'Failed to update password ');
        
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $dataValues = array ('userAccountDetail' => array(
//                 'firstName' => 'Edited firstName',
//                 'middleName' => 'Edited middleName',
//                 'lastName' => 'Edited lastName',
//             )
//         );
        
//         $crawler = $client->request('POST', $editAccountUrl, $dataValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Success! Updated Account');
        
//     }
    
    
}