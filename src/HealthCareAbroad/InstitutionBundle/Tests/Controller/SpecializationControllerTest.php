<?php
/**
 * 
 * @author Chaztine Blance
 *
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
        $crawler = $client->request('POST', '/medical-center/1/specializations/ajaxRemove');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());     
           
        $client = $this->getBrowserWithActualLoggedInUser();
        $extra['common_delete_form'] = array ('extraInvalidField' => 'sdas');
        $crawler = $client->request('POST', '/medical-center/1/specializations/1/ajaxRemove', $extra);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
        $crawler = $client->request('POST', '/medical-center/1/specializations/1/ajaxRemove');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', '/medical-center/3/specializations/1/ajaxRemove');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', '/medical-center/1/specializations/2/ajaxRemove');
        $this->assertEquals(401, $client->getResponse()->getStatusCode());
        
    }
    public function testAjaxAddSpecialization()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', '/ns-institution/ajax/1/loadInstitutionMedicalCenterSpecializations');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('POST', '/ns-institution/ajax/1/loadInstitutionMedicalCenterSpecializations');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testAjaxLoadSpecializationTreatment()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', 'ns-institution/1/ajax/load-specialization-treatments?specializationId=3');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
//     public function testSaveSpecializations()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $extra['institutionSpecialization'] = array (5 => array( 'treatments' => array ( 0 => '6')));
//         $crawler = $client->request('POST', '/medical-center/1/ajaxSave/Specializations', $extra);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//     }

}