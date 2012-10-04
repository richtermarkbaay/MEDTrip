<?php

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class HistoryControllerTest extends InstitutionBundleWebTestCase
{
    public function testShowHistory()
    {
        $editAccountUrl = '/institution/edit-history?objectId=1&objectClass=SGVhbHRoQ2FyZUFicm9hZFxJbnN0aXR1dGlvbkJ1bmRsZVxFbnRpdHlcSW5zdGl0dXRpb25UcmVhdG1lbnQ=';
       
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $editAccountUrl);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Edit History")')->count(), 'Expecting text "Edit History"');
        
    }
}