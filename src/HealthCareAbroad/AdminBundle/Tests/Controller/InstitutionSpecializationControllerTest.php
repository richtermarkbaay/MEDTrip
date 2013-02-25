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

    public function testInvalidIds(){
        //invalid medical center id
        $client = $this->getBrowserWithActualLoggedInUser();
        $invalidUri = '/admin/institution/1/medical-center/2323/specializations/1/ajaxEditInstitutionSpecialization';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $invalidUri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
        //invalid institution id
        $client = $this->getBrowserWithActualLoggedInUser();
        $url = '/admin/institution/1333/medical-center/1/specializations/1/ajaxEditInstitutionSpecialization';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testAjaxRemoveSpecializationTreatment(){
        
        //remove specialization
        $uri = '/admin/institution/1/institutionSpecialization/1/ajaxRemoveSpecializationTreatment/1';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //invalid specialization id passed
        $client = $this->getBrowserWithActualLoggedInUser();
        $uri = '/admin/institution/1/institutionSpecialization/1323/ajaxRemoveSpecializationTreatment/2';
        $client->request('POST', $uri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testajaxViewMedicalSpecializationTreatments(){

        //correct url path
        $uri = '/admin/institution/1/medical-center/1/specializations/1/ajaxEditInstitutionSpecialization';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    
    public function testAddSpecializationTreatment()
    {
        $url = '/admin/institution/1/medical-center/1/specializations/1/ajaxEditInstitutionSpecialization';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $url);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Allergy and Immunology")')->count(), 'No Output!');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test for adding specialization and treatments
        $client = $this->getBrowserWithActualLoggedInUser();
        $formValues = array ( 'institutionSpecialization' => array( '1' => array(
            'treatments' => array(
                    0 => '1',
                    1 => '3' ))));
        $crawler = $client->request('POST', $url, $formValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        // test for empty data
        $client = $this->getBrowserWithActualLoggedInUser();
        $emptyFormValues =  array ();
        $crawler = $client->request('POST', $url, $emptyFormValues);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
        //test for no treatment selected
        $client = $this->getBrowserWithActualLoggedInUser();
        $invalidFormValues = array ( 'institutionSpecialization' => array( '1' => array(
                        'treatments' => '' )));
        $crawler = $client->request('POST', $url, $invalidFormValues);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
    }
    
    public function testAddSpecializationAndTreatment()
    {
        $url =  '/admin/institution/1/medical-center/1/specialization/add';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $url);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Specialization")')->count(), 'No Output!');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //submit specialization and treatments selected
        $client = $this->getBrowserWithActualLoggedInUser();
        $formValues = array ( 'institutionSpecialization' => array( '4' => array(
            'treatments' => array(
                    0 => '5',
        ))));
        $crawler = $client->request('POST', $url, $formValues); 
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); //redirect after submit
        
        // test for empty data
        $client = $this->getBrowserWithActualLoggedInUser();
        $emptyFormValues =  array ( );
        $crawler = $client->request('POST', $url, $emptyFormValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide at least one specialization.")')->count());
        
        // test for no treatment selected
        $client = $this->getBrowserWithActualLoggedInUser();
        $invalidFormValues = array ( 'institutionSpecialization' => array( '4' => array(
            'treatments' => '' //no treatments
        )));
        $crawler = $client->request('POST', $url, $invalidFormValues);
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please provide at least one treatment.")')->count());
        
    }
    
    public function testAjaxAddSpecialization(){
        
        $url =  'admin/institution/1/medical-center/1/ajaxAddSpecialization?specializationId=4';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test invalid specialization id passsed
        $url =  'admin/institution/1/medical-center/1/ajaxAddSpecialization?specializationId=6';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $url);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
}