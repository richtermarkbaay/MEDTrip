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
//         $invalidMethods = array('POST', 'PUT', 'DELETE');
//         $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find(1);
//         $uri = "/admin/institution/{$institution->getId()}/medical-centers";
//         $client = $this->getBrowserWithActualLoggedInUser();
        
//         // test invalid method access
//         foreach ($invalidMethods as $method) {
//             $client->request($method, $uri);
//             $this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Expected method '.$method.' is invalid');
//         }
        
//         $crawler = $client->request('GET', $uri);
//         //echo $client->getResponse(); exit;
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("' .$institution->getName(). ' Medical Centers")')->count(), '"Add Treatment" string not found!');
        
//     }
//     public function testEditMedicalCenterAction()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/edit');
    
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
//         );
//         $form = $crawler->selectButton('submit')->form();
//         $crawler = $client->submit($form, $validValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }
    public function testViewAllMedicalCentersAction()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-centers');
    
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $crawler = $client->request('GET', '/admin/institution/2/medical-centers');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    public function testViewMedicalCenterAction()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/view/1');
    
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testAddMedicalCenterAction()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/add');
        $validValues = array(
                        'institutionMedicalCenter[name]' => 'asd anem',
                        'institutionMedicalCenter[description]' => 'sad description',
                        'institutionMedicalCenter[contactEmail]' => '',
                        'institutionMedicalCenter[contactNumber][country_code]' => '',
                        'institutionMedicalCenter[address][room_number]' => '',
                        'institutionMedicalCenter[address][building]' => '',
                        'institutionMedicalCenter[address][street]' => '',
                        'institutionMedicalCenter[websites][main]' => '',
                        'institutionMedicalCenter[websites][facebook]' => '',
                        'institutionMedicalCenter[websites][twitter]' => '',
                        'institutionMedicalCenter[city]' => 'palakad',
                        'institutionMedicalCenter[zipCode]' => '678001',
                        'institutionMedicalCenter[state]' => 'kerala',
                        'institutionMedicalCenter[country]' => 'india',
                        'institutionMedicalCenter[status]' => '1',
                        'institutionMedicalCenter[timeZone]' => '',
                        'institutionMedicalCenter[businessHours]' => ''
        );
        $invalidValues = array();
        
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidValues);
        //$this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Center name is required.")')->count());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/add');
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $validValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testAddGlobalAwardsAction()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/global_awards');
        
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }
    
    public function testUpdateGlobalAwardsAction()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/global_awards/1');
    
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
    
    
    public function testAddInstitutionTreatmentsAction()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/global_awards/1');
        
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
    public function testEditMedicalCenterStatusAction()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $invalidStatus = 35;
        $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/edit-status', array('status' => $invalidStatus));

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/edit-status');
        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
//         $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/edit-status');
//         $validValues = array('institutionMedicalCenter[status]' => '2');
//         $form = $crawler->selectButton('submit')->form();
//         $crawler = $client->submit($form, $validValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
   }
    
    
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