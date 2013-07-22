<?php

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InquiryControllerTest extends InstitutionBundleWebTestCase
{
//     public function testViewAllInquiries()
//     {
//         $url = "/institution/inquiries";
       
//         $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
//         $crawler = $client->request('GET', $url);
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());

//     }
    
    public function testViewInquiry()
    {
        $url = "/institution/inquiry/1/view";
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
    }
    
    
    
}