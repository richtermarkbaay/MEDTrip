<?php

namespace HealthCareAbroad\FrontendBundle\Tests\Controller;

use HealthCareAbroad\FrontendBundle\Tests\FrontendBundleWebTestCase;

use \HCA_DatabaseManager;

class DefaultControllerTest extends FrontendBundleWebTestCase
{
	
	public function testIndex()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/');
	
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0, $crawler->filter('html:contains("HealthCareAbroad.com")')->count(), '"HealthCareAbroad.com" string not found!');
	}
	
    public function testErrorReport()
    {
    	$client = static::createClient();
    	$crawler = $client->request('GET', '/error');
    	
    	$formValues = array(
    			'ExceptionForm[reporterName]' => 'test Reporter Name',
    			'ExceptionForm[details]' => 'Lorem Lorem ipsum dolor sit amit!'
    	);
 		
 		$invalidFormValues = array(
 				'ExceptionForm[reporterName]' => '',
 				'ExceptionForm[details]' => ''
 		);
      
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Successfully sent error report to HealthCareAbroad")')->count());
        
        $crawler = $client->request('GET', '/error');
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
         
    }
    
}
