<?php
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;
use Symfony\Component\HttpFoundation\FileBag;

use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        $uri = "/institution/medical-center/2/add-existing-doctor";
        $client->request('POST', $uri, array('doctorId' => 12123));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // test for valid doctor
        $uri = "/institution/medical-center/2/add-existing-doctor";
        $client->request('POST', $uri, array('doctorId' => 1));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());        
    }
    
    // NOTE: this test only works if csrf token is set to fasle //
    public function testAjaxupdateDoctor()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $formValues = array('editInstitutionMedicalCenterDoctorForm' => array(
                        'lastName' => 'last',
                        'firstName' => 'first',
                        'middleName' => 'middle',
                        'suffix' => 'Jr.'));
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $uri = "/institution/medical-center/2/update-doctor/1";
        $client->request('POST', $uri, $formValues);
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
    public function testajaxUpdateCoordinates()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $uri = "/institution/medical-center/2/ajax/update-coordinates";
        $client->request('POST', $uri, array('coordinates' => '10.3112791,123.89776089999998'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testUpdateByField()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/institution/listing/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institutionMedicalCenter[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        
        //test form with awards
        $awardValues = array('awardTypeKey' => 'award', 'institutionMedicalCenter' => array(
                        'awards' => array('1'),
                        '_token' => $csrf_token
        ));
        $client->request('POST', "/institution/medical-center/2/ajax/update-by-field", $awardValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test form with biz services
        $servicesValues = array( 'institutionMedicalCenter' => array('services' => array('12'),'_token' => $csrf_token));
        $client->request('POST', "/institution/medical-center/2/ajax/update-by-field", $servicesValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test form with biz hours
        $formValues = array( 'institutionMedicalCenter' => array(
                        'description' => 'testing2',
                        'businessHours' => array('18a6a330-af4d-4371-a4e1-4ca50843847b' => '{"weekdayBitValue":16,"opening":"8:00 AM","closing":"5:00 PM","notes":""}'),
                        '_token' => $csrf_token
        ));
        $client->request('POST', "/institution/medical-center/2/ajax/update-by-field", $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         //test w/o services,awards and bizhours
        $client = $this->getBrowserWithActualLoggedInUserForSingleType();
        $crawler = $client->request('GET', '/institution/listing/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institutionMedicalCenter[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        
        $formValues = array('institutionMedicalCenter' => array(
//                         'contactDetails' =>array ( '0' =>  array ( 'country' => '17', 'area_code' => '343','number' => '434','ext' => '3' )),
                        'address' => array ( 'room_number' => 'test', 'building' => 'test', 'street' => 'test' ),
                        '_token' => $csrf_token
        ));
        $client->request('POST', "/institution/medical-center/2/ajax/update-by-field", $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
//     public function testUploadLogo()
//     {
//         $uri = "/institution/medical-center/2/logo/upload/";
//         $pics = new FileBag();
//         $photo = new UploadedFile('/Users/alniejacobe/Desktop/siya.jpg', 'siya.jpg');
//         $pics->add(array('logo' => '/Users/alniejacobe/Desktop/siya.jpg'));
       
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $client->request('POST', $uri, $pics);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }
}