<?php
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;
/**
 * @author Chaztine Blance
 * CSRF token should be set to false
 */
class MedicalCenterPropertiesControllerTest extends InstitutionBundleWebTestCase
{
    public function testAjaxEditGlobalAward()
    {
        $formVal = array( 'globalAwardId' => 1 , 'propertyId' => 3,'institution_global_award_form' => array(
                        'extraValue' => '2003',
                        'value' =>  '1',
        ));
        
        $uri = "institution/medical-center/14324324/awards-certificates-and-affiliations/ajaxEdit"; //test invalid medical center id
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, $formVal);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        
        $uri = "institution/medical-center/1/awards-certificates-and-affiliations/ajaxEdit";
        $client = $this->getBrowserWithActualLoggedInUser();
        $client->request('POST', $uri, $formVal);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $formVal['institution_global_award_form']['valsadasdsaue'] = 'dasdsd';
        $client->request('POST', $uri, $formVal);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
        //test for invalid global award id
        $client = $this->getBrowserWithActualLoggedInUser();
        $formVal['globalAwardId'] = 454345;
        $client->request('POST', $uri, $formVal);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }    
}