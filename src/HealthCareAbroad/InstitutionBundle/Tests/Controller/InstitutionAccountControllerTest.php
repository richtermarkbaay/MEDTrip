<?php 

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InstitutionAccountControllerTest extends InstitutionBundleWebTestCase
{
	public function testAccount(){
		
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', '/account/1');
		
		// test that we are in the correct page
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
		$this->assertGreaterThan(0,$crawler->filter('h2:contains("Institution Profile Form")')->count());
		
	}
	public function testSave()
	{
		$editAccountUrl = '/create/1';
	
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', '/create/1234567678788345435234324');
		$this->assertEquals(404, $client->getResponse()->getStatusCode());
		
		$client = $this->getBrowserWithActualLoggedInUser();
		$crawler = $client->request('GET', $editAccountUrl);
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	
		$invalidFormValues = array(
						'institutionDetail[contactEmail]' => '',
						'institutionDetail[country]' => '1',
						'institutionDetail[city]' => '1',
						'institutionDetail[address1]' => '',
						'institutionDetail[state]' => '',
						'institutionDetail[zipCode]' => ''
		);
		$form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($form, $invalidFormValues);
		$this->assertEquals('POST', $client->getRequest()->getMethod(), "Expecting POST for form submission");

		$formValues = array(
						'institutionDetail[contactEmail]' => 'tetmail2@fdfewed.com',
						'institutionDetail[country]' => '1',
						'institutionDetail[city]' => '1',
						'institutionDetail[address1]' => '3434',
						'institutionDetail[state]' => '34',
						'institutionDetail[zipCode]' => '3434'
		);
	
		$form = $crawler->selectButton('submit')->form();	
        $crawler = $client->submit($form, $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'Expecting redirect header after submitting data');
        $crawler = $client->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Thank you for registering")')->count(), 'Expecting success message part "Successfully added"');
	}
}