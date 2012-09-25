<?php
/**
 * Functional tests Admin Advertisement controller
 * 
 * @author Allejo Chris G. Velarde
 *
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class AdvertisementControllerTest extends AdminBundleWebTestCase
{
    private $stepOneUri = '/admin/advertisements/add/step-one';
    private $stepTwoUri = '/admin/advertisements/add/step-two';
    
    /**
     * test addBasicDetailAction. This will only cover invalid access. Test for valid flow is in individual test for creating advertisement by type
     */
    public function testAddBasicDetailAction()
    {
        // test for no logged in user access
        $client = $this->requestUrlWithNoLoggedInUser($this->stepOneUri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client), 'Expected redirect to login page');
        
        // test for unauthorized access
        $client = $this->getBrowserWithMockLoggedUser();
        $client->request('GET', $this->stepOneUri);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), 'Expected 403 status code after unauthorized access');
    }
    
    /**
     * test addSpecificDetailAction. This only cover invalid access. Test for valid flow is in individual test for creating advertisement by type
     */
    public function testAddSpecificDetailAction()
    {
        // test for no logged in user access
        $client = $this->requestUrlWithNoLoggedInUser($this->stepTwoUri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client), 'Expected redirect to login page');
        
        // test for unauthorized access
        $client = $this->getBrowserWithMockLoggedUser();
        $client->request('GET', $this->stepTwoUri);
        $this->assertEquals(403, $client->getResponse()->getStatusCode(), 'Expected 403 status code after unauthorized access');
        
        // test for invalid uid set in session
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('GET', $this->stepTwoUri.'?uid=124323538245hfdskghkjsfdhbgkhbjhfgjhw4ti5q3268475gdsu');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals($this->stepOneUri, $this->getLocationResponseHeader($client));
    }
    
    public function testAddNewsTicker()
    {
        // test for step one
        $basicDetailFormValues = array(
            'advertisement[advertisementType]' => AdvertisementTypes::NEWS_TICKER,
            'advertisement[institution]' => 1
        );
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $this->stepOneUri);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Expected 200 status code for good request');
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Create New Advertisement")')->count(), 'h2 text "Create New Advertisement" not found');
        
        // test invalid form submit
        try {
            $crawler = $client->submit($crawler->selectButton('submit')->form(), array(
                    'advertisement[advertisementType]' => 0,
                    'advertisement[institution]' => 0
            ));
        } 
        catch (\Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);    
        }
        
        // test valid form submit
        $crawler = $client->submit($crawler->selectButton('submit')->form(), $basicDetailFormValues);
        $crawler = $client->followRedirect();
        
        // test for step two
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Expected 200 status code for good request');
        $this->assertGreaterThan(0,$crawler->filter('h2:contains("Create News Ticker Advertisement")')->count(), 'h2 text "Create News Ticker Advertisement" not found');
        
        $specificDetailFormValues = array(
            'advertisement[title]' => 'This is a test a news ticker',
            'advertisement[description]' => 'lorem ipsum dolor'
        );
        
        $invalidSpecificDetailFormValues = array(
            'advertisement[title]' => '',
            'advertisement[description]' => ''
        );
        
        // test invalid form submit
        $crawler = $client->submit($crawler->selectButton('submit')->form(), $invalidSpecificDetailFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Expected 200 status code for good request');
        $this->assertGreaterThan(0,$crawler->filter('html:contains("This value should not be blank.")')->count(), '"This value should not be blank." not found');
        
        // test valid form submit
        $crawler = $client->submit($crawler->selectButton('submit')->form(), $specificDetailFormValues);
        $crawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Expected 200 status code for good request');
        $this->assertGreaterThan(0,$crawler->filter('html:contains("Successfully created advertisement. You may now generate invoice.")')->count(), 'Success text "Successfully created advertisement. You may now generate invoice." not found');
        
        // TODO: test for generate invoice and preview page
    }
    
}