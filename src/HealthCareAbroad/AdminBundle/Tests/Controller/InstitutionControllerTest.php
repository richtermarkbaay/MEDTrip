<?php
/**
 * Functional test for Admin InstitutionController
 *
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\AdminBundle\Tests\Controller;

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
        $status = 1;
        $crawler = $client->request('POST', '/admin/institution/1/update-status/'.$status);

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
        $crawler = $client->request('POST', '/admin/institution/1/update-status/'.$invalidStatus);

        $response = $client->getResponse();
        
        $this->assertEquals('/admin/institutions', $response->headers->get('location'));
        $this->assertEquals(302, $response->getStatusCode());

        $crawler = $client->followRedirect(true);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());

        $isNotValidStatus = $crawler->filter('#message-red')->count() > 0;
        $this->assertTrue($isNotValidStatus, 'Invalid status value should not be saved!');
    }

    /////// Manage Institution Medical Centers Tests //
    public function testManageCenters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/manage-centers');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Medical Centers")')->count(), 'No Output!');
    }

    public function testAddMedicalCenter()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/add');

        $formData = array(
            'institutionMedicalCenter[medicalCenter]' => 2,
            'institutionMedicalCenter[description]' => 'This center is added from test.'
        );

        $form = $crawler->selectButton('submit')->first()->form();
        $crawler = $client->submit($form, $formData);

        // check if redirect code 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
         
        // check of redirect url /admin/institution/1/medical-center/edit/{id}
        $this->assertEquals('/admin/institution/1/medical-center/edit/2', $client->getResponse()->headers->get('location'));
         
         
        // redirect request
        $crawler = $client->followRedirect(true);

        // check if the redirected response content has the correct text
        $isAdded = $crawler->filter('#page-heading h2:contains("Edit Medical Center")')->count() > 0;
        $this->assertTrue($isAdded);
    }
    
    public function testAddMedicalCenterAndAddAnother()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/add');

        $formData = array(
            'institutionMedicalCenter[medicalCenter]' => 3,
            'institutionMedicalCenter[description]' => 'This center is added from test and redirect to AddAnother.'
        );
    
        $form = $crawler->selectButton('submit')->last()->form();
        $crawler = $client->submit($form, $formData);
    
        // check if redirect code 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    
        // check of redirect url /admin/institution/1/medical-center/add
        $this->assertEquals('/admin/institution/1/medical-center/add', $client->getResponse()->headers->get('location'));

        // redirect request
        $crawler = $client->followRedirect(true);
    
        // check if the redirected response content has the newly medical center
        $isAdded = $crawler->filter('#page-heading h2:contains("Add Medical Center")')->count() > 0;
        $this->assertTrue($isAdded, 'Incorrect redirect url after adding and add another');
    }

    public function testEditMedicalCenter()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/edit/2');

        $formData = array(
            'institutionMedicalCenter[medicalCenter]' => 2,
            'institutionMedicalCenter[description]' => 'This center is added from test. Updated'
        );

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);
    
        // check if redirect code 302
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    
        // check of redirect url /admin/institution/1/manage-centers
        $this->assertEquals('/admin/institution/1/medical-center/edit/2', $client->getResponse()->headers->get('location'));
    
        // redirect request
        $crawler = $client->followRedirect(true);
    
        // check if the redirected response content has the newly medical center
        $isAdded = $crawler->filter('#message-green')->count() > 0;
        $this->assertTrue($isAdded, 'Failed updating medicalCenter.');
    }

    public function testEditInvalidMedicalCenter()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/edit/10010');
    
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testUpdateInvalidMedicalCenterData()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        
        $postData = array(
            'institutionMedicalCenter[medicalCenter]' => 2,
            'institutionMedicalCenter[description]' => ''
        );

        $crawler = $client->request('POST', '/admin/institution/1/medical-center/edit/2', $postData);    

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
        $this->assertGreaterThan(0, $crawler->filter('form.basic-form > div ul')->count(), 'No validation message!');
    }

    public function testAddDuplicateMedicalCenters()
    {
        $client = $this->getBrowserWithActualLoggedInUser();

        $postParams['institutionMedicalCenter'] = array(
                'medicalCenter' => 2,
                'description' => 'This center is added from test',
                'status' => 1
        );

        $crawler = $client->request('POST', '/admin/institution/1/medical-center/add', $postParams);

        $this->assertNotEquals(302, $client->getResponse()->getStatusCode(), 'institution_medical_centers Duplicate Entry');
    }
    
    public function testSaveMedicalCenterInvalidData()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/add');
    
        $formData = array(
            'institutionMedicalCenter[medicalCenter]' => '',
            'institutionMedicalCenter[description]' => 'This center is added from test. Updated'
        );

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
        $this->assertGreaterThan(0, $crawler->filter('form.basic-form > div ul')->count(), 'No validation message!');
    }
    
    public function testUpdateMedicalCenterInvalidData()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $formData = array(
            'institutionMedicalCenter[medicalCenter]' => '',
            'institutionMedicalCenter[description]' => 'This center is added from test. Updated'
        );
        $crawler = $client->request('POST', '/admin/institution/1/medical-center/edit/2');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Invalid data has been created!');
        $this->assertGreaterThan(0, $crawler->filter('form.basic-form > div ul')->count(), 'No validation message!');
    }

    public function testSaveMedicalCenterInvalidMethod()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        $formData = array(
            'medicalCenter[name]' => 'saveUsingGet',
            'medicalCenter[description]' => 'test invalid medical center method.'
        );

        $crawler = $client->request('GET', '/admin/institution/1/medical-center/test-save', $formData);

        $this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
    
    public function testMedicalCenterUpdateStatus()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $status = 8;
        $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/update-status/' . $status);
    
        $response = $client->getResponse();
        
        // check of redirect url /admin/institution/1/manage-centers
        $this->assertEquals('/admin/institution/1/manage-centers', $client->getResponse()->headers->get('location'));
        $this->assertEquals(302, $response->getStatusCode(), 'Incorrect redirect url');

        $crawler = $client->followRedirect(true);

        $isValidStatus = $crawler->filter('#message-red')->count() == 0;
        $this->assertTrue($isValidStatus, 'Invalid status value ' . $status);
    
        $isStatusUpdated = $crawler->filter('#message-green')->count() > 0;
        $this->assertTrue($isStatusUpdated, 'Unable to update status!');
    }
    
    public function testMedicalCenterUpdateInvalidStatus()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $invalidStatus = 35;
        $crawler = $client->request('POST', '/admin/institution/1/medical-center/1/update-status/'.$invalidStatus);

        $response = $client->getResponse();

        // check of redirect url /admin/institution/1/manage-centers
        $this->assertEquals('/admin/institution/1/manage-centers', $client->getResponse()->headers->get('location'));
        $this->assertEquals(302, $response->getStatusCode(), 'Incorrect redirect url');
        
        $crawler = $client->followRedirect(true);

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    
        $isNotValidStatus = $crawler->filter('#message-red')->count() > 0;
        $this->assertTrue($isNotValidStatus, 'Invalid status value should not be saved!');
    }
    /////// END of Manage Institution Medical Centers Tests //


    /////// Medical Procedure Types Tests //
    public function testAddProcedureType()
    {
        $url = '/admin/institution/1/medical-center/2/procedure-type/add';
        $client = $this->getBrowserWithActualLoggedInUser();
        
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $isCorrectForm = $crawler->filter("#content-table-inner form.basic-form[action='$url']")->count() > 0;
        $this->assertTrue($isCorrectForm, 'Incorrect Add Procedure Type Form');

        $formData = array(
            'institutionMedicalProcedureTypeForm[medicalProcedureType]' => 2,
            'institutionMedicalProcedureTypeForm[description]' => 'This medProceType is added from Admin Add test. got it?'
        );

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);

        $isSuccess = is_object(json_decode($client->getResponse()->getContent()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($isSuccess, 'Unable to save medicalProcedureType');
    }
    
    public function testEditProcedureTypes()
    {
        $url = '/admin/institution/1/medical-center/2/procedure-type/edit/1';
        $client = $this->getBrowserWithActualLoggedInUser();
        
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $isCorrectForm = $crawler->filter("#content-table-inner form.basic-form[action='$url']")->count() > 0;
        $this->assertTrue($isCorrectForm, 'Incorrect Add Procedure Type Form');

        $formData = array(
            'institutionMedicalProcedureTypeForm[medicalProcedureType]' => 2,
            'institutionMedicalProcedureTypeForm[description]' => 'This medProceType is added from Admin Add test got it? yes, updated!'
        );

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);

        $isSuccess = is_object(json_decode($client->getResponse()->getContent()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($isSuccess, 'Unable to save medicalProcedureType');
    }

    public function testEditInvalidProcedureType()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/procedure-type/edit/10010');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testSaveInvalidDataProcedureType()
    {
        $url = '/admin/institution/1/medical-center/1/procedure-type/add';
        $client = $this->getBrowserWithActualLoggedInUser();

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    
        $isCorrectForm = $crawler->filter("#content-table-inner form.basic-form[action='$url']")->count() > 0;
        $this->assertTrue($isCorrectForm, 'Incorrect Add Procedure Type Form');

        $formData = array(
            'institutionMedicalProcedureTypeForm[medicalProcedureType]' => 1,
            'institutionMedicalProcedureTypeForm[description]' => ''
        );

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);
    
        $isNotSuccess = !is_object(json_decode($client->getResponse()->getContent()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($isNotSuccess, 'Invalid Data should not be saved!');
    }

    public function testSaveProcedureTypeInvalidMethod()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        $formData = array(
            'institutionMedicalProcedureTypeForm[medicalProcedureType]' => 'saveUsingGet',
            'institutionMedicalProcedureTypeForm[description]' => 'test invalid medical center method.'
        );

        $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/procedure-type/test-save', $formData);
    
        $this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }
    /////// END of Manage Institution Medical Procedure Types Tests //



    /////// Medical Procedure Tests //
    public function testAddProcedure()
    {
        $url = '/admin/institution/1/medical-center/2/procedure-type/1/procedure/add';
        $client = $this->getBrowserWithActualLoggedInUser();
        
        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $isCorrectForm = $crawler->filter("#content-table-inner form.basic-form[action='$url']")->count() > 0;
        $this->assertTrue($isCorrectForm, 'Incorrect Add Procedure Type Form');

        $formData = array(
            'institutionMedicalProcedureForm[medicalProcedure]' => 3,
            'institutionMedicalProcedureForm[description]' => 'This Procedure is added from Admin Add test. got it?',
            'institutionMedicalProcedureForm[status]' => 1
        );

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);

        $isSuccess = is_object(json_decode($client->getResponse()->getContent()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($isSuccess, 'Unable to save medicalProcedure');
    }
    
    public function testEditProcedure()
    {
        $url = '/admin/institution/1/medical-center/2/procedure-type/1/procedure/edit/1';
        $client = $this->getBrowserWithActualLoggedInUser();

        $crawler = $client->request('GET', $url);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $isCorrectForm = $crawler->filter("#content-table-inner form.basic-form[action='$url']")->count() > 0;
        $this->assertTrue($isCorrectForm, 'Incorrect Add Procedure Type Form');

        $formData = array(
            'institutionMedicalProcedureForm[medicalProcedure]' => 3,
            'institutionMedicalProcedureForm[description]' => 'This Procedure is added from Admin Add test. got it? updated',
            'institutionMedicalProcedureForm[status]' => 1
        );

        $form = $crawler->selectButton('submit')->form();
        $crawler = $client->submit($form, $formData);

        $isSuccess = is_object(json_decode($client->getResponse()->getContent()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($isSuccess, 'Unable to save medicalProcedure');
    }
    
    public function testSaveProcedureInvalidData()
    {
        $url = '/admin/institution/1/medical-center/2/procedure-type/1/procedure/add';
        $client = $this->getBrowserWithActualLoggedInUser();
    
        $formData = array(
            'institutionMedicalProcedureForm[medicalProcedure]' => 3,
            'institutionMedicalProcedureForm[description]' => '',
            'institutionMedicalProcedureForm[status]' => 1
        );

        $crawler = $client->request('POST', $url, $formData);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $isNotSuccess = !is_object(json_decode($client->getResponse()->getContent()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($isNotSuccess, 'Invalid Data should not be saved!');
    }

    public function testUpdateProcedureInvalidData()
    {
        $url = '/admin/institution/1/medical-center/2/procedure-type/1/procedure/edit/1';
        $client = $this->getBrowserWithActualLoggedInUser();

        $formData = array(
            'institutionMedicalProcedureForm[medicalProcedure]' => 3,
            'institutionMedicalProcedureForm[description]' => '',
            'institutionMedicalProcedureForm[status]' => 1
        );

        $crawler = $client->request('POST', $url, $formData);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $isNotSuccess = !is_object(json_decode($client->getResponse()->getContent()));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($isNotSuccess, 'Invalid Data should not be saved!');
    }

    public function testEditInvalidProcedureData()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/2/procedure-type/1/procedure/edit/10010');
    
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testSaveProcedureInvalidMethod()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
    
        $formData = array(
            'institutionMedicalProcedureForm[medicalProcedure]' => 'saveUsingGet',
            'institutionMedicalProcedureForm[description]' => 'test invalid medical center method.',
            'institutionMedicalProcedureForm[status]' => 1
        );
    
        $crawler = $client->request('GET', '/admin/institution/1/medical-center/1/procedure-type/1/procedure/test-save', $formData);
    
        $this->assertEquals(405, $client->getResponse()->getStatusCode(), 'Invalid method accepted!');
    }

    public function testProcedureUpdateStatus()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $status = 1;
        $crawler = $client->request('POST', '/admin/institution/procedure/1/update-status/' . $status);

        $response = $client->getResponse();

        $this->assertEquals("true", $response->getContent());
    
    }
    /////// END of Medical Procedure Tests //
    
}