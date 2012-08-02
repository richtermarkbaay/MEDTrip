<?php
/**
 * Functional test for InstitutionUserController
 * 
 * @author Allejo Chris G. Velarde
 *
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InstitutionUserControllerTest extends WebTestCase
{
    private $userEmail = 'test.user@chromedia.com';
    private $userPassword = '123456';
    
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
        
        // assert that it was redirected to /institution/login after clicking the submitting wrong login
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('/institution/login', $client->getResponse()->headers->get('location'));
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
}