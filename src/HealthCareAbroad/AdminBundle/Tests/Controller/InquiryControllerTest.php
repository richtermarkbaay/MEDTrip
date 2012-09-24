<?php
/**
 * Functional test for Admin InquiryController
 *
 * @author Alnie Jacobe
 *
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Entity\Inquiry;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InquiryControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/inquiries');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("List of Inquiries")')->count(), 'No Output!');
    }
}