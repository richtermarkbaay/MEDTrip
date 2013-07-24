<?php
/*
 * 
 * @author Alnie Jacobe
 */

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InquiryControllerTest extends InstitutionBundleWebTestCase
{
    public function testViewAllInquiries()
    {
        $url = "/institution/inquiries";
       
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
    
    public function testViewInquiry()
    {
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        
        //TEST FPR VALID INQUIRY ID
        $url = "/institution/inquiry/1/view";        
        $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test for invalid inquiry id
        $url = "/institution/inquiry/123/view";
        $client->request('GET', $url);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testRemoveInquiry()
    {
        $url = "/institution/inquiry/1/ajaxRemoveInquiry";

        //test for valid inquiry id
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $client->request('POST', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test for invalid inquiry id
        $url = "/institution/inquiry/123/ajaxRemoveInquiry";
        $client->request('POST', $url);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
    }
    
    public function testAjaxSetInquiryStatus()
    {
        $uri = "/institution/inquiry/ajaxSetInstitutionInquiryStatus";
        $params = array('status' => 1, 'inquiryListArr' => 1);
        
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $client->request('POST', $uri, $params);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}