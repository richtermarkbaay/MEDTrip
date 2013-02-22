<?php
/**
 * @author Chaztine Blance
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class InstitutionSpecializationControllerTest extends AdminBundleWebTestCase
{
    
    public function testInvalidTreatmentId(){
        // test invalid treatmend Id
        $client = $this->getBrowserWithActualLoggedInUser();
        $uri = '/admin/institution/1/institutionSpecialization/1/ajaxRemoveSpecializationTreatment/343434';
        $client->request('POST', $uri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testAjaxRemoveSpecializationTreatment(){
        
        $uri = '/admin/institution/1/institutionSpecialization/1/ajaxRemoveSpecializationTreatment/1';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $uri = '/admin/institution/1/institutionSpecialization/1323/ajaxRemoveSpecializationTreatment/2';
        $client->request('POST', $uri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testajaxViewMedicalSpecializationTreatments(){
        
        $uri = '/admin/institution/1/medical-center/1/specializations/1/ajaxEditInstitutionSpecialization';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        
        $invalidUri = '/admin/institution/2323/medical-center/2323/specializations/1/ajaxEditInstitutionSpecialization';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $invalidUri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testAddSpecializationTreatment()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $url = '/admin/institution/1/medical-center/1/specializations/1/ajaxEditInstitutionSpecialization';
        $crawler = $client->request('GET', $url);
        
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Allergy and Immunology")')->count(), 'No Output!');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        
        $formValues = array ( 'institutionSpecialization' => array( '1' => array(
            'treatments' => array(
                    0 => '1',
                    1 => '3'
        ))));
        
        $crawler = $client->request('POST', $url, $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $invalidFormValues = array ( 'institutionSpecialization' => array( '1' => array(
                        'treatments' => array(
                                        0 => '1',
                                        1 => '5' // 5 does not exist
                        )
        )));
        
        $crawler = $client->request('POST', $url, $invalidFormValues);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    
//     public function testAddSpecializationAndTreatment()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $url = '/admin/institution/1/medical-center/1/specializations/1/ajaxEditInstitutionSpecialization';
//         $crawler = $client->request('GET', $url);
    
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Allergy and Immunology")')->count(), 'No Output!');
//         $extract = $crawler->filter('input')->extract(array('value'));
//         $csrf_token = str_replace('\"','',$extract[1]);
    
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
    
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $postUrl = '/admin/institution/1/medical-center/1/specialization/add';
//         $formValues = array( '1' => array(
//                         'treatments' => array(
//                                 '0' => '1',
//                                 '_token' => $csrf_token
//                         )
//         )
//         );
    
//         $crawler = $client->request('POST', $postUrl, $formValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }
    
}