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
    
    public function testEdit()
    {
        $uri = '/admin/doctor/edit/1';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        //test invalid doctor account id
        $uri = '/admin/doctor/edit/3123123';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testSave()
    {
        $uri = '/admin/doctor/save/1';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $formValues = array(
                        'doctor[firstName]' => 'testFirstName',
                        'doctor[middleName]' => 'testMiddleName',
                        'doctor[lastName]' => 'testLastName',
                        'doctor[specializations]' => array(1),
                        'doctor[contactEmail]' => 'testaccount@yahoo.com',
                        'doctor[contactNumber]' => '[{"number":"2123123123123","type":"mobile"}]'
                        );
        $crawler = $client->request('GET', $uri);
        $referer = $client->getRequest()->headers->get('referer');
        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formValues);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
        //test invalid account 
        $uri = '/admin/doctor/save/3124124';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
        
        //add new account
        $uri = '/admin/doctor/save';
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        
    }
}