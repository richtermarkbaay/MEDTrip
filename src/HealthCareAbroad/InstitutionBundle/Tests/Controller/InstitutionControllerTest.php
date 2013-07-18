<?php
/**
 * Functional test for InstitutionController
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InstitutionControllerTest extends InstitutionBundleWebTestCase
{
	
    public function testViewAllStaffAction()
    {
        $viewAllStaffUrl = '';
    }
   
    
	/*public function testEditInformation()
	{ 
		$editAccountUrl = '/institution/edit-information/1';
		
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
		
        $formValues = array(
        		'institutionDetail[name]' => 'edit name',
        		'institutionDetail[description]' => 'edit desc',
        		'institutionDetail[country]' => '1',
        		'institutionDetail[city]' => '1',
        		'institutionDetail[address1]' => 'edit address1',
                'institutionDetail[institutionLanguagesSpoken]' => 'English'
        );
		
        $invalidFormValues = $formValues;
        $invalidFormValues['institutionDetail[name]'] = null;
        
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
           
        //test for invalid description
        $invalidFormValues = $formValues;
        $invalidFormValues['institutionDetail[description]'] = null;
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues); // test submission of invalid form values
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');
 
        //test valid values
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $editAccountUrl);
        $crawler = $client->submit($form, $formValues);
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Successfully updated account")')->count());
        //---- end test edit logged in account
        
		
		
		
	}*/
	
}