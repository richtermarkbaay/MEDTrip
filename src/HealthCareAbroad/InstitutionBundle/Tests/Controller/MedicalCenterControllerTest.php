<?php

namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

use Symfony\Component\HttpFoundation\Session\Session;

use \HCA_DatabaseManager;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class MedicalCenterControllerTest extends InstitutionBundleWebTestCase
{
    public function testIndex()
    {
        $uri = '/institution/medical-centers';

        // test for no login user
        $client = $this->requestUrlWithNoLoggedInUser($uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client), 'Expecting redirect to login page');

        // test for correct logged user
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('title:contains("Institution Panel Specializations")')->count());

        // test with invalid institution id
        $this->setInvalidInstitutionInSession($client);
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $uri = '/institution/medical-centers/add';

        // test for no login user
        $client = $this->requestUrlWithNoLoggedInUser($uri);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client), 'Expecting redirect to login page');

        // test for correct logged user
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('h1:contains("Add specialization")')->count(), "Expecting header 'Add specialization'");

        // test for invalid form submission
        $invalidFormValues = array(
            'institutionMedicalCenter[medicalCenter]' => '',
            'institutionMedicalCenter[description]' => '',
            //'institutionMedicalCenter[status]' => 1
        );
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $invalidFormValues);
        $this->assertEquals('POST', $client->getRequest()->getMethod(), "Expecting POST for form submission");
        $this->assertGreaterThan(0, $crawler->filter('html:contains("This value should not be blank.")')->count(), 'Expecting the validation message "This value should not be blank."');

        // test valid form submission
        $validFormValues = array(
            'institutionMedicalCenter[medicalCenter]' => 3,
            'institutionMedicalCenter[description]' => 'testsetsdfdsfdsfafsafsadf',
            //'institutionMedicalCenter[status]' => 1
        );
        $crawler = $client->submit($form, $validFormValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'Expecting redirect header after submitting data');
        $crawler = $client->followRedirect();
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Successfully added")')->count(), 'Expecting success message part "Successfully added"');

    }

    public function testSave()
    {
        $client = $this->getBrowserWithActualLoggedInUser();

        // --- test not allowed methods
        //$client->request('GET', '/institution/medical-centers/save');
        //$this->assertEquals(405, $client->getResponse()->getStatusCode(), "POST is the only allowed method");

        //$client->request('PUT', '/institution/medical-centers/save');
        //$this->assertEquals(405, $client->getResponse()->getStatusCode(), "POST is the only allowed method");

        //$client->request('DELETE', '/institution/medical-centers/save');
        //$this->assertEquals(405, $client->getResponse()->getStatusCode(), "POST is the only allowed method");

        // -- test posting with invalid imcId
        $client->request('POST', '/institution/medical-centers/edit/99999999');
        $this->assertEquals(404, $client->getResponse()->getStatusCode(), "Expecting 404 if passed invalid imcId");
    }


    public function testEdit()
    {
        $uri = '/institution/medical-centers/edit/1';

        // test for no login user
        $client = $this->requestUrlWithNoLoggedInUser($uri);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($this->isRedirectedToLoginPage($client), 'Expecting redirect to login page');

        $client = $this->getBrowserWithActualLoggedInUser();

        // test invalid id
        $client->request('GET', $uri.'99999999999');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        // test for valid id
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0,$crawler->filter('title:contains("Edit Specialization")')->count(), "Expecting title 'Edit Specialization'");

        // test valid form submission
        $validFormValues = array(
            'institutionMedicalCenter[description]' => 'testsetsdfdsfdsfafsafsadf',
            //'institutionMedicalCenter[status]' => 1
        );

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $validFormValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode(), 'Expecting redirect header after submitting data');
    }
}