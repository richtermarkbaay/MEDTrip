<?php
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class MedicalCenterPropertiesControllerTest extends InstitutionBundleWebTestCase
{
    public function testajaxRemoveGlobalAward()
    {
        $uri = "/institution/medical-center/2/awards-certificates-and-affiliations/ajaxRemove";
    
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 1));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test for invalid global award id
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 1123));
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }    
}