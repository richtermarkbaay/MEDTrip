<?php
/**
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class InstitutionTreatmentsControllerTest extends AdminBundleWebTestCase
{
//     public function testPreExecute()
//     {
//         $uri = '/admin/institution/123123123123312312312312323231/medical-centers';
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $client->request('GET', $uri);
//         $this->assertEquals(404, $client->getResponse()->getStatusCode(), 'Expected not found after invalid institutionId');
//     }

//     public function testViewAllMedicalCentersAction()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', '/admin/institution/1/medical-centers');
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         //test for singleCenter
//         //$crawler = $client->request('GET', '/admin/institution/2/medical-centers');
//         //$this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }
    
//     public function testAddMedicalCenterAction()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', '/admin/institution/1/medical-center/add');
//         $validValues = array(
//                         'institutionMedicalCenter[name]' => 'asd anem',
//                         'institutionMedicalCenter[description]' => 'sad description',
//                         'institutionMedicalCenter[contactEmail]' => '',
//                         'institutionMedicalCenter[contactNumber][country_code]' => '',
//                         'institutionMedicalCenter[address][room_number]' => '',
//                         'institutionMedicalCenter[address][building]' => '',
//                         'institutionMedicalCenter[address][street]' => '',
//                         'institutionMedicalCenter[websites][main]' => '',
//                         'institutionMedicalCenter[websites][facebook]' => '',
//                         'institutionMedicalCenter[websites][twitter]' => '',
//                         'institutionMedicalCenter[city]' => 'palakad',
//                         'institutionMedicalCenter[zipCode]' => '678001',
//                         'institutionMedicalCenter[state]' => 'kerala',
//                         'institutionMedicalCenter[country]' => 'india',
//                         'institutionMedicalCenter[status]' => '1',
//                         'institutionMedicalCenter[timeZone]' => '',
//                         'institutionMedicalCenter[businessHours]' => ''
//         );
//         $invalidValues = array();
    
//         $form = $crawler->selectButton('submit')->form();
//         $crawler = $client->submit($form, $invalidValues);
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Center name is required.")')->count());
    
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', '/admin/institution/1/medical-center/add');
//         $form = $crawler->selectButton('submit')->form();
//         $crawler = $client->submit($form, $validValues);
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
//     }
//     public function testViewMedicalCenterAction()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         echo "REQUEST /admin/institution/1/medical-center/view/1 \n";
//         $crawler = $client->request('GET', '/admin/institution/1/medical-center/view/1');
    
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }

//     public function testEditMedicalCenterAction()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/edit');
    
//         $validValues = array(
//                         'institutionMedicalCenter[name]' => 'ako nasdad alsdkj',
//                         'institutionMedicalCenter[description]' => 'sad description',
//                         'institutionMedicalCenter[contactEmail]' => '',
//                         'institutionMedicalCenter[contactNumber][country_code]' => '',
//                         'institutionMedicalCenter[address][room_number]' => '',
//                         'institutionMedicalCenter[address][building]' => '',
//                         'institutionMedicalCenter[address][street]' => '',
//                         'institutionMedicalCenter[websites][main]' => '',
//                         'institutionMedicalCenter[websites][facebook]' => '',
//                         'institutionMedicalCenter[websites][twitter]' => '',
//         );
//         $form = $crawler->selectButton('submit')->form();
//         $crawler = $client->submit($form, $validValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }
    
//     public function testEditMedicalCenterStatusAction()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/edit');
//         $extract = $crawler->filter('input[name="institutionMedicalCenter[_token]"]')->extract(array('value'));
//         $csrf_token = $extract[0];
        
//         $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/edit-status', array('institutionMedicalCenter' => array('status' => 1, '_token' => $csrf_token)));
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());

//         $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/edit-status');
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//    }
   
//    public function testLoadMedicalSpecialistAction()
//    {
//        $client = $this->getBrowserWithActualLoggedInUser();
//        $crawler = $client->request('GET', '/ns-admin/institution/1/medical-center/1/medical-specialists/load?term=an');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//    }
   
//    public function testAjaxAddMedicalSpecialistAction()
//    {
//        $client = $this->getBrowserWithActualLoggedInUser();
       
//        //add valid medicalSpecialist
//        $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/medical-specialists/add',array('id' => 2));
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
       
//        //test for invalid medicalSpecialist
//        $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/medical-specialists/add',array('id' => 21));
//        $this->assertEquals(404, $client->getResponse()->getStatusCode());
       
//        //test for existing medicalSpecialist
//        $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/medical-specialists/add',array('id' => 1));
//        $this->assertEquals(500, $client->getResponse()->getStatusCode());
       
//    }
    
//    public function testAjaxRemoveMedicalSpecialistAction()
//    {
//        $client = $this->getBrowserWithActualLoggedInUser();
//        $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/ajaxRemoveMedicalSpecialist/2');
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
       
//         //test for valid form       
//        $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/edit');
//        $extract = $crawler->filter('input[name="institutionMedicalCenter[_token]"]')->extract(array('value'));
//        $csrf_token = $extract[0];
//        $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/ajaxRemoveMedicalSpecialist/2',array('common_delete_form' => array('_token' => $csrf_token, 'id' => 2)));
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
       
//        //test for invalid form
//        $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/ajaxRemoveMedicalSpecialist/2',array('common_delete_form' => array('id' => 2)));
//        $this->assertEquals(400, $client->getResponse()->getStatusCode());
       
//        //test for invalid medicalSpecialist Id
//        $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/ajaxRemoveMedicalSpecialist/21',array('common_delete_form' => array('_token' => $csrf_token, 'id' => 21)));
//        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
//    }
   
//     public function testCenterSpecializationAction()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/specializations');
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }
//     public function testAjaxAddAncillaryServiceAction()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         echo "REQUEST /admin/institution/1/medical-center/1/ajaxAddAncillaryService?asId=3 \n";
//         $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/ajaxAddAncillaryService?asId=3');
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         echo  $client->getResponse();
//     }
    
//     public function testAjaxRemoveAncillaryServiceAction()
//     {
//        //$client = $this->getBrowserWithActualLoggedInUser();
//        //$crawler = $client->request('POST', '/admin/institution/1/medical-center/1/ajaxRemoveAncillaryService?asId=1');
//        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }
    
//     public function testAjaxAddGlobalAwardAction()
//     {
// //         $client = $this->getBrowserWithActualLoggedInUser();
// //         $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/awards-certificates-and-affiliations/ajaxAdd?id=1');
// //         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }
//     public function testAddMedicalCenterDetailsAction()
//     {
//         $uri = '/admin/institution/1/medical-centers';
        
//         $client = $this->getBrowserWithActualLoggedInUser();
        
//         // test invalid methods
//         $this->commonTestForInvalidMethodRequests($client, $uri, array('PUT', 'DELETE'));
        
//         $crawler = $client->request('GET', $uri);
//         $this->assertGreaterThan(0, $crawler->filter('form:contains("Name")')->count(), 'Expecting field Name');
//         $this->assertGreaterThan(0, $crawler->filter('form:contains("Details")')->count(), 'Expecting field Details');
        
//         // test missing fields
//         $form = $crawler->selectButton('submit')->first()->form();
//         $crawler = $client->submit($form, array(
//             'institutionMedicalCenter[name]' => '',
//             'institutionMedicalCenter[description]' => ''
//         ));
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('form:contains("Name")')->count(), 'Expecting validation error `Center name is required.`');
//         $this->assertGreaterThan(0, $crawler->filter('form:contains("Details")')->count(), 'Expecting validation error `Center details is required.`');
        
//         // test correct form fields
//         $form = $crawler->selectButton('submit')->first()->form();
//         $crawler = $client->submit($form, array(
//             'institutionMedicalCenter[name]' => 'this is a test medical center',
//             'institutionMedicalCenter[description]' => 'test description only'
//         ));
//         $crawler = $client->followRedirect();
//     }
}