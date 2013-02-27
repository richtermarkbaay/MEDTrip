<?php
/**
 * @author Chaztine Blance
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class InstitutionPropertiesControllerTest extends AdminBundleWebTestCase
{
    public function testInvalidIds() 
    {
        //invalid institution id
        $client = $this->getBrowserWithActualLoggedInUser();
        $invalidUri = '/admin/institution/2323/ancilliary-services';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $invalidUri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testIndex()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $url = '/admin/institution/1/ancilliary-services';
        $crawler = $client->request('GET', $url);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Ancilliary Services")')->count(), 'No Output!');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
//     public function testView()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $url = '/admin/institution/1/global-awards';
//         $crawler = $client->request('GET', $url);
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Global Awards / Certifications")')->count(), 'No Output!');
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//     }

    public function testAjaxAddAncilliaryService(){
        
        $url = '/admin/institution/1/add-ancilliary-service';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $formValues = array ('asId' => 2);
        $crawler = $client->request('GET', $url, $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $formValues = array ( 'asId' => '0');
        $crawler = $client->request('GET', $url, $formValues);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
//     public function testDuplicateAncillaryService()
//     {
//         $url = '/admin/institution/1/add-ancilliary-service';
//         //test added duplicate offered service
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $formValues = array ( 'asId' => 1);
//         $crawler = $client->request('GET', $url, $formValues);
//         $this->assertEquals(500, $client->getResponse()->getStatusCode());
//     }

}