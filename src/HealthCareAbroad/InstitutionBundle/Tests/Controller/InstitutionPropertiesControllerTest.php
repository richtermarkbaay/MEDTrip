<?php
/**
 * Functional test for InstitutionPropertiesController
 * 
 * @author Alnie Jacobe
 * Runs only when csrf_protection is set to false
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InstitutionPropertiesControllerTest extends InstitutionBundleWebTestCase
{
    
	public function testEditGlobalAward()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $uri = "/institution/awards-certificates-and-affiliations/ajaxEdit";
        $client->request('POST', $uri, array('propertyId' => 1, 'globalAwardId' => 1, ));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    
        $client = $this->getBrowserWithActualLoggedInUser();
        //test for invalid property
        $uri = "/institution/awards-certificates-and-affiliations/ajaxEdit";
        $client->request('POST', $uri, array('propertyId' => 123, 'globalAwardId' => 1));
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
        //test for invalid global award id
        $client = $this->getBrowserWithActualLoggedInUser();
        $uri = "/institution/awards-certificates-and-affiliations/ajaxEdit";
        $client->request('POST', $uri, array('propertyId' => 1, 'globalAwardId' => 12312));
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testRemoveGlobalAward()
    {
        $uri = "/institution/awards-certificates-and-affiliations/ajaxRemove";
    
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 1));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test for invalid global award id
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 1123));
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testAjaxAddAncillaryService()
    {
        $uri = "/institution/ajaxAddAncillaryService";
    
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 1));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test for existing ancillary service id
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 1));
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
        
        //test fpr invalid anciallar service id
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 1231));
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testajaxRemoveAncillaryService()
    {
        $uri = "/institution/ajaxRemoveAncillaryService";
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 2));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test for invalid service id
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 2));
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    
    
    
    
    
}