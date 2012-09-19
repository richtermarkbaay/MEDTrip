<?php

namespace HealthCareAbroad\PageBundle\Tests\Controller;

use HealthCareAbroad\PageBundle\Tests\PageBundleWebTestCase;

use \HCA_DatabaseManager;

class DefaultControllerTest extends PageBundleWebTestCase
{
   
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
