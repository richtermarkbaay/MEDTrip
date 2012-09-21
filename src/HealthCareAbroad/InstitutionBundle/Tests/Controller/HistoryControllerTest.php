<?php

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class HistoryControllerTest extends InstitutionBundleWebTestCase
{
    public function testShowHistory()
    {
        $editAccountUrl = '/institution/medical-centers';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        
//         $crawler = $client->request('GET', $editAccountUrl);
//         var_dump($client->getResponse()->getContent()); exit;
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}