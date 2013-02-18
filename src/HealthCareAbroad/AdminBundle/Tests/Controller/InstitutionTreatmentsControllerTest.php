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
    
    public function testViewAllMedicalCentersAction()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-centers');
    
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testViewMedicalCenterAction()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/view/1');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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