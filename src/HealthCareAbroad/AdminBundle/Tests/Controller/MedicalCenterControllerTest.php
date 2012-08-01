<?php
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MedicalCenterControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/medical-centers');

        $this->assertTrue($crawler->filter('html:contains("product-table")')->count() > 0);
    }
}