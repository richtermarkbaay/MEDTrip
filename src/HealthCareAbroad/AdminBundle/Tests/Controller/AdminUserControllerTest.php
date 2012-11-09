<?php
/**
 * Functional test for AdminUserController
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class AdminUserControllerTest extends AdminBundleWebTestCase
{
    public function testLoginAndLogoutFlow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admin/login');
        
        //---- correct login flow
        // test we are in the login page and status code is 200
        $this->assertGreaterThan(0, $crawler->filter('title:contains("Admin Login")')->count());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // test submit with correct credentials
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array('_username' => 'admin_authorized', '_password' => '123456'));
        
        
        // assert that it was redirected to /admin
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('http://localhost/admin', $client->getResponse()->headers->get('location'));
        //---- end correct login flow
        
        //---- logout flow validation
        // request to /admin
        $crawler = $client->request('GET', '/admin');
        $logoutLink = $crawler->selectLink('logout')->eq(0)->link();
        $crawler = $client->click($logoutLink); // click logout link
        
        // assert that it was redirected to /admin/login after clicking the logout link
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client));
        //---- end logout flow
        
        //---- login with invalid credentials
        // request to login page and test for invalid login
        $crawler = $client->request('GET', '/admin/login');
        
        // test submit with invalid credentials
        $formValues = array('_username' => 'admin_authorized', '_password' => '123123213212'.time());
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Bad credentials")')->count());
        //---- end login with invalid credentials
        
        //---- login with missing required fields
        $crawler = $client->request('GET', '/admin/login');
        
        // test submit with missing required fields
        $formValues = array();
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, array());
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Bad credentials")')->count()); // look for the text "Bad credentials"
        //---- end login with missing required fields -->
    }
    
    public function testViewAllUsers()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/settings/users');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Admin users")')->count());
        
        // test that 403 must be returned when an authenticated user with no valid roles acesses the page
        $client = $this->getBrowserWithMockLoggedUser();
        $token = $client->getContainer()->get('security.context')->getToken();
        $crawler = $client->request('GET', '/admin/settings/users');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
        
    }
    
    public function testEditAccount()
    {
    	$editAccountUrl = '/admin/edit-account';
		
		//---- test that this should not be accessed by anonymous user
		$client = $this->requestUrlWithNoLoggedInUser($editAccountUrl);
		$this->assertEquals(302, $client->getResponse()->getStatusCode());
		//---- end test that this should not be accessed by anonymous user
		
		//---- test edit logged in account
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', $editAccountUrl);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		
		$formValues = array(
				'adminUserForm[firstName]' => 'edit first',
				'adminUserForm[middleName]' => 'edit middle',
				'adminUserForm[lastName]' => 'edit last',
		);
		
		//test for firstName = null
		$invalidFormValues = $formValues;
		$invalidFormValues['adminUserForm[firstName]'] = null;
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
		 
		//test for valid form
		$crawler = $client->request('GET', $editAccountUrl);
		$referer = $client->getRequest()->headers->get('referer'); 
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $formValues);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		//---- end test edit logged in account
		
		// test edit invalid logged in account id
		$session = $client->getContainer()->get('session');
		$session->set('accountId', 999999);
		$session->save();
		
		$crawler = $client->request('GET', $editAccountUrl);
		$this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testChangePassword()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/change-password');
        
    	
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Current Password")')->count()); // look for the Current Password text
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("New Password")')->count()); // look for the New Password text
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Confirm Password")')->count()); // look for the Confirm Password text
    	
    	$form = $crawler->selectButton('submit')->form();
    	$formValues = array(
    			'adminUserChangePasswordType[current_password]' => $this->userPassword,
    			'adminUserChangePasswordType[new_password]' => $this->userPassword.'1',
    			'adminUserChangePasswordType[confirm_password]' => $this->userPassword.'1',
    	);
    	$crawler = $client->submit($form, $formValues);
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('/admin', $client->getResponse()->headers->get('location'));
        
        // test change password invalid form
        $crawler = $client->request('GET', '/admin/change-password');
        $form = $crawler->selectButton('submit')->form();
        $formValues = array(
            'adminUserChangePasswordType[current_password]' => $this->userPassword,
            'adminUserChangePasswordType[new_password]' => $this->userPassword.'1',
            'adminUserChangePasswordType[confirm_password]' => $this->userPassword.'23',
        );
        $crawler = $client->submit($form, $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Passwords do not match")')->count(), 'Expecting the validation message "Passwords do not match"');
        
        // test edit invalid logged in account id
        $session = $client->getContainer()->get('session');
        $session->set('accountId', 999999);
        $session->save();
        
        $crawler = $client->request('GET', '/admin/change-password');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}