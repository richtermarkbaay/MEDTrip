<?php

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MedicalProcedureControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/medical-procedures');
var_dump('xx='. $crawler->filter('h3:contains("List of Medical Procedures")')->count() . '--');
        $this->assertTrue($crawler->filter('h3:contains("List of Medical Procedures")')->count() > 0);
    }
}
