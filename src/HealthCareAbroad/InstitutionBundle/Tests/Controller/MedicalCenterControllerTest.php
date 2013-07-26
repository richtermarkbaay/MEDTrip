<?php
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class MedicalCenterControllerTest extends InstitutionBundleWebTestCase
{
    public function testIndex()
    {
        $uri = "/institution/listings";
        
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $client->request('GET', $uri);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testView()
    {
        $uri = "/institution/listing/2";
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('GET', $uri);
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function getToken()
    {
        $uri = '/institution/listings';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institutionMedicalCenter[_token]"]')->extract(array('value'));
        return $extract[0];
    }
    
    public function testAddMedicalCenter()
    {
        $uri = '/institution/listings';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institutionMedicalCenter[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        $formValues =  array( 'institutionMedicalCenter' => array(
                        'name' => 'testing2',
                        'contactEmail' => 'test11312123@yahoo.com',
                        'contactDetails' =>array ( '0' =>  array ( 'country_code' => '358', 'area_code' => '343','number' => '434','ext' => '3' )),
                        '_token' => $csrf_token
                        ));
        
        $uri = "/institution/medical-center/add-new";
        $client->request('POST', $uri, $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        
        //test invalid formvalues
        $client->request('POST', $uri, array('institutionMedicalCenter' => array()));
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    
    public function testAddDoctor()
    {
        $uri = '/institution/listing/2';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institutionMedicalCenterDoctor[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        
        $uri = "/institution/medical-center/2/add-doctor";
        $formValues =  array('institutionMedicalCenterDoctor' => array(
                        'lastName' => 'last',
                        'firstName' => 'first',
                        'middleName' => 'middle',
                        'suffix' => 'Jr.',
                        '_token' => $csrf_token
        ));
        
        $client->request('POST', $uri, $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test for invalid form
        $client->request('POST', $uri, array('institutionMedicalCenterDoctor' => array()));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testAddExistingDoctor()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        
        //test for invalid doctor
        $uri = "/institution/medical-center/add-existing-doctor/2";
        $client->request('POST', $uri, array('doctorId' => 12123));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // test for valid doctor
        $uri = "/institution/medical-center/add-existing-doctor/2";
        $client->request('POST', $uri, array('doctorId' => 1));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    
    
    public function testAjaxupdateDoctor()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $uri = "/institution/medical-center/2/update-doctor/1";
        $client->request('POST', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testRemoveDoctor()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        
        //test for valid doctor
        $uri = "/institution/medical-center/2/remove-doctor?doctorId=2";
        $client->request('POST', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test for invalid doctor
        $uri = "/institution/medical-center/2/remove-doctor?doctorId=1231232";
        $client->request('POST', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    
}