<?php
/**
 * 
 * @author Chaztine Blance
 * NOTE: before running the test, CSRF token should be set to false
 */
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

use Symfony\Component\HttpFoundation\Session\Session;

use \HCA_DatabaseManager;

use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class SpecializationControllerTest extends InstitutionBundleWebTestCase
{
    public function testAjaxRemoveSpecialization()
    {

        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', '/medical-center/1/specializations/345345/ajaxRemove');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());     
           
        $client = $this->getBrowserWithActualLoggedInUser();
        $extra['common_delete_form'] = array ('extraInvalidField' => 'sdas');
        $crawler = $client->request('POST', '/medical-center/1/specializations/1/ajaxRemove', $extra);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', '/medical-center/3/specializations/1/ajaxRemove');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', 'medical-center/1/specializations/1/ajaxRemove');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ), '{"id":"1"}');
        $this->assertRegExp('/id/', $client->getResponse()->getContent());
        
    }
    public function testAjaxAddSpecialization()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/ns-institution/ajax/1/loadInstitutionMedicalCenterSpecializations');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ));
        $this->assertRegExp('/html/', $client->getResponse()->getContent());

        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', '/ns-institution/ajax/1/loadInstitutionMedicalCenterSpecializations');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testAjaxLoadSpecializationTreatments()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $extra['specializationId'] = '1';
        $crawler = $client->request('GET', 'ns-institution/1/ajax/load-specialization-treatments/1', $extra);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ));
        $this->assertRegExp('/html/', $client->getResponse()->getContent());
    }

    /* NOTE: Before running the test, set CSRF token to true. */
    public function testSaveSpecializations()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        
        $extra['institutionSpecialization'] = array (); // no treatment data
        $crawler = $client->request('POST', '/medical-center/1/ajaxSave/Specializations', $extra);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
        $extra['institutionSpecialization'] = array (5 => array( 'treatments' => array ( 0 => '6')));
        $crawler = $client->request('POST', '/medical-center/1/ajaxSave/Specializations', $extra);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ));
        $this->assertRegExp('/html/', $client->getResponse()->getContent());
    }

    public function testAjaxAddInstitutionSpecializationTreatments()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        
        $invalidForm['institutionSpecialization'] = array (); // no treatment data
        $crawler = $client->request('POST', 'medical-center/1/specializations/1/ajaxEditInstitutionSpecialization', $invalidForm);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $form['institutionSpecialization'] = array (1 => array( 'treatments' => array ( 0 => '1')));
        $form['deleteTreatments']= '1';
        $crawler = $client->request('POST', 'medical-center/1/specializations/1/ajaxEditInstitutionSpecialization', $form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ), '{"specializations":{"1":{"treatments":["1"]}}}');
        $this->assertRegExp('/specializations/', $client->getResponse()->getContent());
        
        //invalid specialization id
        $client = $this->getBrowserWithActualLoggedInUser();
        $form['institutionSpecialization'] = array (1 => array( 'treatments' => array ( 0 => '1')));;
        $crawler = $client->request('POST', 'medical-center/1/specializations/13232323/ajaxEditInstitutionSpecialization', $form);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    /* End of NOTE */
}