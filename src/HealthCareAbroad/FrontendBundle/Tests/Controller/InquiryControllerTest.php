<?php
/**
 * Functional test for InquiryController
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\FrontendBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\FrontendBundle\Tests\FrontendBundleWebTestCase;

class InquiryControllerTest extends FrontendBundleWebTestCase
{
	public function testIndexAction()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/inquire');
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Inquiry subject")')->count()); // look for the inquiry subject text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Message")')->count()); // look for the message text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Firstname")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Lastname")')->count()); // look for the Current name text
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Email")')->count()); // look for the New email text
	
		$form = $crawler->selectButton('submit')->form();
	
		$formValues = array(
				'inquire[inquiry_subject]' => '2',
				'inquire[message]' => 'test test',
				'inquire[firstName]' => 'test name',
				'inquire[lastName]' => 'last',
				'inquire[email]' => 'test@yahoo.com',
		);
		$crawler = $client->submit($form, $formValues);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		//$this->assertEquals('/inquire', $client->getResponse()->headers->get('location'));
	
		// test for missing fields flow
		$crawler = $client->request('GET', '/inquire');
		$formValues = array(
				'inquire[inquiry_subject]' => '2',
				'inquire[message]' => 'test test',
				'inquire[firstName]' => 'test name',
				'inquire[lastName]' => '',
				'inquire[email]' => 'test@yahoo.com',
		);
	
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $formValues);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
	}
}

?>