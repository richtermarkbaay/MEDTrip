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
    
}