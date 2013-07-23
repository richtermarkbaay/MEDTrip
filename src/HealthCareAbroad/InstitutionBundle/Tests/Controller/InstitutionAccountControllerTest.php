<?php 
/**
 * Functional Test for InstitutionAccountController
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InstitutionAccountControllerTest extends InstitutionBundleWebTestCase
{
    private $profileFormValues =  array( 'institution_profile_form' => array(
        'name' => 'new name',
        'address1' => array ( 'room_number' => 'test', 'building' => 'test', 'street' => 'test' ),
        'country' => '11',
        'city' => '61914',
        'description' => 'test',
        'state' => 'test test',
        'zipCode' => '232',
        'contactEmail' => 'test@yahoo.com',
        'contactDetails' =>array ( '0' =>  array ( 'country_code' => '358', 'area_code' => '343','number' => '434','ext' => '3' )),
        'addressHint' => 'test',
        'medicalProviderGroups' => array( '0' => ''),
        'coordinates' => '10.3112791,123.89776089999998',
        'socialMediaSites' => array ( 'facebook' => 'test', 'twitter' => 'test','googleplus' => 'test' ),
        '_token' => '',
    ));
    
    public function testAjaxUpdateProfileByField()
    {
        $client = $this->getBrowserWithActualLoggedInUserForSingleType();
        $invalidProfileFormValues =  array( 'institution_profile_form' => array( 'name' => null));
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $invalidProfileFormValues);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
        $uri = '/institution/profile.html';
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        $this->profileFormValues['institution_profile_form']['_token'] = $csrf_token;
        
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $this->profileFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $invalidAwardsFormValues =  array( 'institution_profile_form' => array( ));
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $invalidAwardsFormValues);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
//         $awardsFormValues =  array( 'institution_profile_form' => array('awards' => array ( 0 => '1' ,1 => '2' ) ));
//         $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $awardsFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testMultipleProfile(){
    
        $uri = '/institution/profile.html';
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testAjaxUpdateCoordinates(){
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $formValue =  array( 'coordinates' => '23423423423423');
        $crawler = $client->request('POST', '/institution/ajax/update-coordinates', $formValue);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    
}