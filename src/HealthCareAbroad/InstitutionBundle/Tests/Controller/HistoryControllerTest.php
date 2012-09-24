<?php

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class HistoryControllerTest extends InstitutionBundleWebTestCase
{
    public function testShowHistory()
    {
        $editAccountUrl = '/institution/edit-history?objectId=1&objectClass=SGVhbHRoQ2FyZUFicm9hZFxJbnN0aXR1dGlvbkJ1bmRsZVxFbnRpdHlcSW5zdGl0dXRpb25NZWRpY2FsUHJvY2VkdXJlVHlwZQ';
       
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $editAccountUrl);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode()); // test that it has been redirected to the referer
    }
}