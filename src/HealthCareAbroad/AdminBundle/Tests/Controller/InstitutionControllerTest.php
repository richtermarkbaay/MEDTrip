<?php
/**
 * Functional test for Admin InstitutionController
 *
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InstitutionControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institutions');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('#page-heading > h3')->text() == 'List of Institutions', 'No Output!');
    }
    
    public function testView()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/view');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testViewInvalidInstitution()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/10010/view');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testUpdateStatus()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $status = InstitutionStatus::ACTIVE;
        $crawler = $client->request('POST', '/admin/institution/1/update-status', array('status' => $status));

        $response = $client->getResponse();

        // check of redirect url /admin/institutions
        $this->assertEquals('/admin/institutions', $client->getResponse()->headers->get('location'));
        $this->assertEquals(302, $response->getStatusCode());

        $crawler = $client->followRedirect(true);

        $isValidStatus = $crawler->filter('#message-red')->count() == 0;
        $this->assertTrue($isValidStatus, 'Invalid status value ' . $status);

        $isStatusUpdated = $crawler->filter('#message-green')->count() > 0;
        $this->assertTrue($isStatusUpdated, 'Unable to update status!');
    }
    
    public function testUpdateInvalidStatus()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $invalidStatus = 35;
        $crawler = $client->request('POST', '/admin/institution/1/update-status', array('status' => $invalidStatus));

        $response = $client->getResponse();
        
        $this->assertEquals('/admin/institutions', $response->headers->get('location'));
        $this->assertEquals(302, $response->getStatusCode());

        $crawler = $client->followRedirect(true);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $isNotValidStatus = $crawler->filter('#message-red')->count() > 0;
        $this->assertTrue($isNotValidStatus, 'Invalid status value should not be saved!');
    }
    
}