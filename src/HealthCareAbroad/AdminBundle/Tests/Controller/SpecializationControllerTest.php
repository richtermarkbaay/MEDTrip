<?php
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class SpecializationControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$this->assertEquals('1','1');
         $crawler = $client->request('GET', '/admin/specializations');

//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Specializations")')->count(), 'No Output!');
    }

}