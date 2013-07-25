<?php
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class MedicalCenterControllerTest extends InstitutionBundleWebTestCase
{
    
    
    public function testIndex()
    {
        $uri = "/institution/listings";
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('GET', $uri);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    
    public function testView()
    {
//         $uri = "institution/listing/2";
        
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $client->request('GET', $uri);
        
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testajaxUpdateByField()
    {
//         $uri = "/institution/medical-center/2/ajax/update-by-field";
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $client->request('POST', $uri, array());
    }
    
    public function testAddMedicalCenter()
    {
//         $formValues =  array( 'institutionMedicalCenter' => array(
//                         'name' => 'testing2',
//                         'contactEmail' => 'test11312123@yahoo.com',
//                         'contactDetails' =>array ( '0' =>  array ( 'country_code' => '358', 'area_code' => '343','number' => '434','ext' => '3' )),
//                         '_token' => ''
//                         ));
        
//         $uri = "/institution/medical-center/add-new";
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $client->request('POST', $uri, $formValues);

//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
                
    }
}