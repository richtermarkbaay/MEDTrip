<?php
/**
 * Functional test for Admin MedicalProceduController
 *
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;

use HealthCareAbroad\AdminBundle\Tests\AdminBundleWebTestCase;

class TreatmentControllerTest extends AdminBundleWebTestCase
{
    public function testIndex()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/treatments');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Treatments")')->count(), 'Wrong page header!');
    }

    public function testAdd()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/treatment/add');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Add Treatment")')->count(), '"Add Treatment" string not found!');
    }
    
    public function testAddWithProcedureType()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/treatment/add?subSpecializationId=2');
        $isCorrectSelected = (int)$crawler->filter('#treatment_subSpecializations > option[selected]')->attr('value') === 2;
        $this->assertTrue($isCorrectSelected, 'Incorrect selected procedure type!');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testAddInvalidProcedureType()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/treatment/add?subSpecializationId=10010');
    
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testEdit()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/treatment/edit/1');

    	$this->assertEquals(200, $client->getResponse()->getStatusCode());
    	$this->assertGreaterThan(0, $crawler->filter('html:contains("Edit Treatment")')->count(), '"Edit Treatment" string not found!');
    }
    
    public function testEditInvalidProcedure()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/treatment/edit/10010');
    
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testAddSave()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/treatment/add');
    
    	$formData = array(
    		'treatment[name]' => 'testeste new',
            'treatment[description]' => 'description test',
   			'treatment[subSpecializations]' => array(2),
    		'treatment[status]' => 1
    	);

     	$form = $crawler->selectButton('submit')->first()->form();
     	$crawler = $client->submit($form, $formData);
     	
     	// check if redirect code 302
    	$this->assertEquals(302, $client->getResponse()->getStatusCode());

    	// check of redirect url /admin/treatment/edit/{id}
    	$this->assertEquals('/admin/treatment/edit/3', $client->getResponse()->headers->get('location'));

    	// redirect request
		$crawler = $client->followRedirect(true);
		
		// check if the redirected response content has the newly added procedure name
		$isAdded = $isAdded = $crawler->filter('#page-heading > h2:contains("Edit Treatment")')->count() > 0;
    	$this->assertTrue($isAdded);
    }
    
//     public function testAddSaveAnddAddAnother()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', '/admin/treatment/add');
    
//         $formData = array(
//             'treatment[name]' => 'testeste new withAddAnother',
//             'treatment[description]' => 'description test',
//             'treatment[subSpecializations]' => 2,
//             'treatment[status]' => 1
//         );
    
//         $form = $crawler->selectButton('submit')->last()->form();
//         $crawler = $client->submit($form, $formData);
    
// //         check if redirect code 302
//         $this->assertEquals(302, $client->getResponse()->getStatusCode());
         
//         // check of redirect url /admin/treatment/add
//         $this->assertEquals('/admin/treatment/add', $client->getResponse()->headers->get('location'));
         
         
//         // redirect request
//         $crawler = $client->followRedirect(true);
    
//         // check if the redirected response content has the newly added procedure name
// 		$isAdded = $isAdded = $crawler->filter('#page-heading > h2:contains("Add Treatment")')->count() > 0;
//         $this->assertTrue($isAdded);
//     }

//     public function testEditSave()
//     {
//     	$client = $this->getBrowserWithActualLoggedInUser();
//     	$crawler = $client->request('GET', '/admin/treatment/edit/1');

//     	$formData = array(
//     			'treatment[name]' => 'testeste new updated',
//     			'treatment[subSpecializations]' => 2,
//                 'treatment[description]' => 'description test',
//     			'treatment[status]' => 1
//     	);

//     	$form = $crawler->selectButton('submit')->first()->form();
//     	$crawler = $client->submit($form, $formData);

//     	// check if redirect code 302
//     	$this->assertEquals(302, $client->getResponse()->getStatusCode());

//     	// check of redirect url /admin/treatment/edit/{id}
//     	$this->assertEquals('/admin/treatment/edit/1', $client->getResponse()->headers->get('location'));

//     	// redirect request
//     	$crawler = $client->followRedirect(true);

//     	// check if the redirected response content has the newly added procedure name
//         $isAdded = $isAdded = $crawler->filter('#page-heading > h2:contains("Edit Treatment")')->count() > 0;
//     	$this->assertTrue($isAdded);
//     }
    
    public function testEditSaveInvalidData()
    {
        $client = $this->getBrowserWithActualLoggedInUser();

        $postData = array(
            'treatment[name]' => '',
            'treatment[description]' => '',
            'treatment[subSpecialization]' => 2,
            'treatment[status]' => 1
        );
        $crawler = $client->request('POST', '/admin/treatment/edit/1', $postData);

    	$this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
    	$this->assertGreaterThan(0, $crawler->filter('form.basic-form > div ul')->count(), 'No validation message!');
    }

    public function testCreateDuplicate()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/treatment/add');
    
    	$formData = array(
			'treatment[name]' => 'testeste new updated',
            'treatment[description]' => 'description',
			'treatment[subSpecializations]' => 2,
    		'treatment[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	// check if status code is not 302
    	$this->assertNotEquals(302, $client->getResponse()->getStatusCode(), '"Treatment Procedure" must not be able to create an entry with duplicate ("medical_procedure_type_id", "name") fields.');
    }
    
    public function testUpdateStatusAction(){
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/treatment/update-status/1');

    	$response = $client->getResponse();
    	$this->assertEquals("Response code: 200", "Response code: " . $response->getStatusCode());
    }
   

    public function testSaveInvalidData()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();
    	$crawler = $client->request('GET', '/admin/treatment/add');

    	$formData = array(
			'treatment[name]' => '',
			'treatment[subSpecializations]' => 2,
			'treatment[status]' => 1
    	);

    	$form = $crawler->selectButton('submit')->form();
    	$crawler = $client->submit($form, $formData);

    	$this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
    	$this->assertGreaterThan(0, $crawler->filter('form.basic-form > div ul')->count(), 'No validation message!');
    }
    
    public function testSaveInvalidDataWithProcedureType()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        
        $formData = array(
            'treatment[name]' => '',
            'treatment[subSpecializations]' => 2,
            'treatment[status]' => 1
        );

        $crawler = $client->request('POST', '/admin/treatment/add?subSpecializationId=10010', $formData);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
        $this->assertGreaterThan(0, $crawler->filter('form.basic-form > div ul')->count(), 'No validation message!');
    }

    public function testSaveInvalidMethod()
    {
    	$client = $this->getBrowserWithActualLoggedInUser();

    	$formData = array(
			'treatment[name]' => 'saveUsingGet',
			'treatment[subSpecializations]' => 2,
			'treatment[status]' => 1
    	);
    	$crawler = $client->request('GET', '/admin/treatment/test-save', $formData);
    	$this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }

    public function testIndexWithFilters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        // Test Filter Active Status
        $crawler = $client->request('GET', '/admin/treatments?status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllActive = $crawler->filter('#treatment-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isAllActive, 'ListFilter is not working properly!');

        // Test Filter Inactive Status
        $crawler = $client->request('GET', '/admin/treatments?status=0');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isAllInactive = $crawler->filter('#treatment-list tr a.icon-2')->count() == 0;
        $this->assertEquals(true, $isAllInactive, 'ListFilter is not working properly!');
    
        // Test Filter subSpecialization
        $crawler = $client->request('GET', '/admin/treatments?subSpecialization=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isFiltered = $crawler->filter('#treatment-list tr > td.procedure-type:not(:contains("Procedure Type1"))')->count() == 0;
        $this->assertEquals(true, $isFiltered, 'Filter procedureType is not working properly!');

        // Test Filter status And subSpecialization
        $crawler = $client->request('GET', '/admin/treatments?subSpecialization=2&status=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $isFiltered = $crawler->filter('#treatment-list tr > td.procedure-type:not(:contains("Test Proc Type with center2"))')->count() == 0;
        $isAllActive = $crawler->filter('#treatment-list tr a.icon-5')->count() == 0;
        $this->assertEquals(true, $isFiltered && $isAllActive, 'Filter procedureType and status is not working properly!');
    }
}


