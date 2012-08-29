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

class InstitutionUserControllerTest extends InstitutionBundleWebTestCase
{
    /**
     * Functional test for login and logout flow
     */
    public function testLoginAndLogoutFlow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/institution/login');
        
        //---- correct login flow
        // test we are in the login page and status code is 200
        $this->assertGreaterThan(0, $crawler->filter('title:contains("Institution Login")')->count());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // test submit with correct credentials
        $formValues = array('userLogin[email]' => $this->userEmail, 'userLogin[password]' => $this->userPassword);
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        
        // assert that it was redirected to /institution
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('/institution', $client->getResponse()->headers->get('location'));
        //---- end correct login flow
        
        //---- logout flow validation
        // request to /institution
        $crawler = $client->request('GET', '/institution');
        $logoutLink = $crawler->selectLink('logout')->eq(0)->link();
        $crawler = $client->click($logoutLink); // click logout link
        
        // assert that it was redirected to /institution/login after clicking the logout link
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('/institution/login', $client->getResponse()->headers->get('location'));
        //---- end logout flow
        
        //---- login with invalid credentials
        // request to login page and test for invalid login
        $crawler = $client->request('GET', '/institution/login');
        
        // test submit with invalid credentials
        $formValues = array('userLogin[email]' => $this->userEmail, 'userLogin[password]' => $this->userPassword.'123123213212');
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Either your email or password is wrong.")')->count()); // look for the text "Email and password are required."
        //---- end login with invalid credentials
        
        //---- login with missing required fields
        $crawler = $client->request('GET', '/institution/login');
        
        // test submit with missing required fields
        $formValues = array();
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array());
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Email and password are required.")')->count()); // look for the text "Email and password are required."
        //---- end login with missing required fields -->
    }
    
    /**
     * Functional test for change password flow
     */
    public function testChangePasswordFlow()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/institution/change-password');
        
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Current Password")')->count()); // look for the Current Password text
        $this->assertGreaterThan(0, $crawler->filter('html:contains("New Password")')->count()); // look for the New Password text
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Confirm Password")')->count()); // look for the Confirm Password text

        $form = $crawler->selectButton('submit')->form();
        $formValues = array(
            'institutionUserChangePasswordType[current_password]' => $this->userPassword,
            'institutionUserChangePasswordType[new_password]' => $this->userPassword.'1',
            'institutionUserChangePasswordType[confirm_password]' => $this->userPassword.'1',
        );
        $crawler = $client->submit($form, $formValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Password changed!")')->count());
        
        // revert the chromedia global accounts fixtures after changing the password
        \HCA_DatabaseManager::getInstance()->restoreGlobalAccountsDatabaseState();
    }
    
    
    public function testInviteFlow()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/institution/invite-staff');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Invite staff")')->count(), 'No "Invite staff string found"'); // look for the Current Password text
        
        $formValues = array(
            'institutionUserInvitation[firstName]' => 'AAA',
            'institutionUserInvitation[middleName]' => 'BBB',
            'institutionUserInvitation[lastName]' => 'CCC',
            'institutionUserInvitation[firstName]' => 'AAA',
            'institutionUserInvitation[email]' => 'aaa@chromedia.com',
            'institutionUserInvitation[message]' => 'this is the message',
        );
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('/institution/staff', $client->getResponse()->headers->get('location'));
        
        // test for missing fields flow
        $crawler = $client->request('GET', '/institution/invite-staff');
        $formValues = array(
            'institutionUserInvitation[firstName]' => 'AAA',
            'institutionUserInvitation[middleName]' => 'BBB',
            'institutionUserInvitation[lastName]' => 'CCC',
            'institutionUserInvitation[firstName]' => 'AAA',
            'institutionUserInvitation[email]' => '',
            'institutionUserInvitation[message]' => 'this is the message',
        );
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
    }
    
    
    public function testAcceptInvitation()
    {
        $client = static::createClient();
        $uri = '/accounts/accept-invitation/94f348d1f65c54cae854b22e5fcc949b408da4682efd9567a66fdbe8323595b7';
        $crawler = $client->request('GET', $uri);

        $this->assertEquals(302, $client->getResponse()->getStatusCode()); // test that it has been redirected to homepage
        $this->assertEquals('/institution', $client->getResponse()->headers->get('location'));
    }
    
    /**
     * @depends testAcceptInvitation
     */
    public function testAcceptInvalidInvitation()
    {
        $client = static::createClient();
        $uri = '/accounts/accept-invitation/94f348d1f65c54cae854b22e5fcc949b408da4682efd9567a66fdbe8323595b7';
        $crawler = $client->request('GET', $uri);
        
        // test that it should be a 404 error code since this token has already been accepted
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testViewAllStaffFlow()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/institution/staff');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Staff")')->count(), 'Cannot find string "List of Staff"');
    }
    
    public function testEditAccount()
    {
        $editAccountUrl = '/institution/edit-account';
        //---- test that this should not be accessed by anonymous user
        $client = $this->requestUrlWithNoLoggedInUser($editAccountUrl);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $redirectLocation = $client->getResponse()->headers->get('location');
        $this->assertTrue($redirectLocation=='/institution/location' || $redirectLocation == 'http://localhost/institution/login');
        //---- end test that this should not be accessed by anonymous user
        
        //---- test edit logged in account
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $editAccountUrl);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Edit my account")')->count(), "Cannot find the 'Edit my account' header text");
        
        $formValues = array(
            'userAccountDetail[firstName]' => 'Edited firstName',
            'userAccountDetail[middleName]' => 'Edited middleName',
            'userAccountDetail[lastName]' => 'Edited lastName',
        );
        
        $invalidFormValues = $formValues;
        $invalidFormValues['userAccountDetail[firstName]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
        
        $crawler = $client->request('GET', $editAccountUrl);
        $referer = $client->getRequest()->headers->get('referer');
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); // test that it has been redirected to the referer
        $this->assertEquals($referer, $client->getResponse()->headers->get('location'));
        //---- end test edit logged in account
        
        //---- test edit invalid account
        $client->request('GET', $editAccountUrl.'/12345678234');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
    }
}