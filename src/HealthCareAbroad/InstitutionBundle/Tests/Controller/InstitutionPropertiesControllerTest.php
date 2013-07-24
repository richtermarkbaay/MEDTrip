<?php
/**
 * Functional test for InstitutionProperiesController
 * 
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InstitutionPropertiesControllerTest extends InstitutionBundleWebTestCase
{
	
    public function testAjaxAddAncillaryService()
    {
        $uri = "/institution/ajaxAddAncillaryService";
    
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 1));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testEditGlobalAward()
    {
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $uri = "/institution/awards-certificates-and-affiliations/ajaxEdit";
        $client->request('POST', $uri, array('propertyId' => 1, 'globalAwardId' => 1));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         $client = $this->getBrowserWithActualLoggedInUser();
//         //test for invalid property
//         $uri = "/institution/awards-certificates-and-affiliations/ajaxEdit";
//         $client->request('POST', $uri, array('propertyId' => 123, 'globalAwardId' => 1));
//         $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testRemoveGlobalAward()
    {
        $uri = "/institution/awards-certificates-and-affiliations/ajaxRemove";
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, array('id' => 1));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    
}