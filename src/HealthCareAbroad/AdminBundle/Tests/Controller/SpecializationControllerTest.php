<?php
namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SpecializationControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
        $uri = '/admin/specializations';
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Specializations")')->count(), 'No Output!');
    }
    
    public function testAdd()
    {
        $uri = '/admin/specialization/add';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
    
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Specialization")')->count(), '"Add Specialization" string not found!');
    }
    
    public function testEdit()
    {
        $uri = '/admin/specialization/edit/1';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
    
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Medical Center")')->count(), '"Edit Medical Center" string not found!');
    }
    
    public function testUpdateStatusAction()
    {
        $uri = '/admin/specialization/update-status/1';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
    
        $response = $client->getResponse();
        $this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
    
    public function testAddSave()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/specialization/add');
        $file = new UploadedFile('/Users/Chaztine/Pictures/peanut-butter-sandwich.gif', 'peanut-butter-sandwich.gif', 'image/jpeg', 50,149);
        $formData = array(
                        'specialization[name]' => 'addedby Institution fromtest',
                        'specialization[media]' => $file,
                        'specialization[description]' => 'The quick brown fox added from test.',
                        'specialization[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->first()->form();
        $crawler = $client->submit($form, $formData);
    
        // check if redirect code 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    
        // check of redirect url /admin/medical-centers
        $this->assertEquals('/admin/specialization/edit/5', $client->getResponse()->headers->get('location'));
    
        // redirect request
        $crawler = $client->followRedirect(true);
    
        // check if the redirected response content has the newly added medical center
        $isAdded = $crawler->filter('h4:contains("Edit Specialization")')->count() > 0;
        $this->assertTrue($isAdded);
    
        $isMessageShow = $crawler->filter('div.alert-success')->count() > 0;
        $this->assertTrue($isMessageShow, 'Success notice not found!');
    }
    public function testSaveAndAddAnother()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/specialization/add');
    
        $formData = array(
                        'specialization[name]' => 'addedby Institution fromtest',
                        'specialization[description]' => 'The quick brown fox added from test.',
                        'specialization[status]' => 1
        );
    
        $form = $crawler->selectButton('Save & Add another Specialization')->last()->form();
        $crawler = $client->submit($form, $formData);
    
        // check if redirect code 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    
        // check of redirect url /admin/medical-centers
        $this->assertEquals('/admin/specialization/add', $client->getResponse()->headers->get('location'));
    
        // redirect request
        $crawler = $client->followRedirect(true);
    
        // check if the redirected response content has the newly added medical center
        $isAdded = $crawler->filter('h4:contains("Add Specialization")')->count() > 0;
        $this->assertTrue($isAdded);
    
        $isMessageShow = $crawler->filter('div.alert-success')->count() > 0;
        $this->assertTrue($isMessageShow, 'Save message does not show!');
    }
    public function testEditSave()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/specialization/edit/1');
    
        $formData = array(
                        'specialization[name]' => 'addedby Institution fromtest updated',
                        'specialization[description]' => 'The quick brown fox added from test. Updated from test.',
                        'specialization[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);
    
        // check if redirect code 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    
        // check of redirect url /admin/medical-centers
        $this->assertEquals('/admin/specialization/edit/1', $client->getResponse()->headers->get('location'));
    
        // redirect request
        $crawler = $client->followRedirect(true);
    
        // check if the redirected response content has the newly added medical center name
        $isAdded = $crawler->filter('h4:contains("Edit Specialization")')->count() > 0;
        $this->assertTrue($isAdded);
    
        $isMessageShow = $crawler->filter('div.alert-success')->count() > 0;
        $this->assertTrue($isMessageShow, 'Save message does not show!');
    }
    public function testCreateDuplicate()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/specialization/add');
    
        $formData = array(
                        'specialization[name]' => 'addedby Institution fromtest',
                        'specialization[description]' => 'The quick brown fox added from test.',
                        'specialization[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);
    
        // check if status code is not 302
        $this->assertNotEquals(302, $client->getResponse()->getStatusCode(), '"Specialization" must not be able to create an entry with duplicate name.');
    }
    public function testSaveInvalidData()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/specialization/add');
    
        $formData = array(
                        'specialization[name]' => '',
                        'specialization[description]' => 'test invalid medical center data.',
                        'specialization[status]' => 1
        );
    
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);
    
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
        $this->assertGreaterThan(0, $crawler->filter('form.basic-form > div ul')->count(), 'No validation message!');
    }
    
    public function testSaveInvalidMethod()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        $formData = array(
                        'specialization[name]' => 'saveUsingGet',
                        'specialization[description]' => 'test invalid medical center method.',
                        'specialization[status]' => 1
        );
        $crawler = $client->request('GET', '/admin/specialization/test-save', $formData);
        $this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
    
    public function testLoadAvailableSubSpecializations()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $url = '/ns-admin/specialization/load-available-sub-specializations/1';
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}