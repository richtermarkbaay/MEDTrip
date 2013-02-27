<?php 

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class DoctorControllerTest extends AdminBundleWebTestCase 
{
    public function testIndex()
    {
        $uri = '/admin/doctors';
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $uri = '/admin/doctor/add';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    public function testSearchMedicalSpecialistSpecialization()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/doctor/loadSpecializations/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    public function testEdit()
    {
        //test invalid doctor account id
        $uri = '/admin/doctor/edit/3123123';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
        //test with non exiting doctor id
        $uri = '/admin/doctor/edit/0';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $uri = '/admin/doctor/edit/1';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    
    public function testUpdateStatus()
    {
        $uri = '/admin/doctor/update-status/1';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testSaveMedia()
    {
        
    }
    
    public function testSave()
    {
        //test invalid account 
        $uri = '/admin/doctor/save/3124124';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
        //test for non existing doctor
        $uri = '/admin/doctor/save/0';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        
        //test for valid doctor and data
        $uri = '/admin/doctor/save/1';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $formValues = array('doctor[media]' => 'Desktop/pics/siya.jpg');
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}