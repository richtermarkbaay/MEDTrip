<?php 
/**
 * Functional Test for InstitutionAccountController
 * @author Chaztine Blance
 * Set CSRF token to true before running this test
 */
namespace HealthCareAbroad\InstitutionBundle\Tests\Controller;

use \HCA_DatabaseManager;
use HealthCareAbroad\InstitutionBundle\Tests\InstitutionBundleWebTestCase;

class InstitutionAccountControllerTest extends InstitutionBundleWebTestCase
{
    private $profileUri = '/institution/profile.html';
//     private $profileFormValues =  array('institution_profile_form' => array(
//         'name' => 'new nameasas',
//         'address1' => array ( 'room_number' => 'test', 'building' => 'test', 'street' => 'test' ),
//         'country' => '1',
//         'city' => '1',
//         'description' => 'test',
//         'state' => '1',
//         'zipCode' => '232',
//         'contactEmail' => 'test@yahoo.com',
//         'addressHint' => 'test',
//         'medicalProviderGroups' => array( '0' => 'test'),
//         'coordinates' => '10.3112791,123.89776089999998',
//         'socialMediaSites' => array ( 'facebook' => 'test', 'twitter' => 'test','googleplus' => 'test' ),
// //         'contactDetails' =>array ( '0' =>  array ( 'country_code' => '358', 'area_code' => '343','number' => '434','ext' => '3' )), //disabled as it cause error
//         '_token' => '',
                    
//     ));
    
//     public function testSingleProfile()
//     {
//         $uri = '/institution/profile.html';
        
//         $client = $this->getBrowserWithActualLoggedInUserForSingleType();
//         $crawler = $client->request('GET', $uri);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         $this->setInvalidInstitutionInSession($client);
//         $crawler = $client->request('GET', $uri);
//         $this->assertEquals(404, $client->getResponse()->getStatusCode());
//     }
    
//     public function testUpdateProfileNameAndMedicalProviderGroupForm()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', $this->profileUri);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $extract = $crawler->filter('form#nameModalForm input[id="institution_profile_form__token"]')->extract(array('value'));
//         $csrf_token = $extract[0];
//         $profileFormValues =  array('institution_profile_form' => array('_token' => $csrf_token, 'name' => 'new nameasas', 'medicalProviderGroups' => array( 0 => 'test')));
//         $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $profileFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ), '{"name":"new nameasas","medicalProviderGroups":"["test"]"}');
//         $this->assertRegExp('/institution/', $client->getResponse()->getContent());
        
//         $profileFormValues =  array('institution_profile_form' => array('_token' => $csrf_token, 'name' => 'new nameasas', 'medicalProviderGroups' => array( 0 => ''))); //test for empty medical provider
//         $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $profileFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ), '{"name":"new nameasas"}');
//         $this->assertRegExp('/institution/', $client->getResponse()->getContent());
        
//         //test invalid form
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $profileFormValues =  array('institution_profile_form' => array('_token' => $csrf_token, 'name' => null, 'medicalProviderGroups' => array( 0 => '')));
//         $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $profileFormValues);
//         $this->assertEquals(400, $client->getResponse()->getStatusCode());
//         $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ), '{"field":"name","error":"Please provide your hosipital name."}');
//         $this->assertRegExp('/errors/', $client->getResponse()->getContent());
//     }
//     public function testUpdateDescriptionForm()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', $this->profileUri);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $extract = $crawler->filter('form#descriptionModalForm input[id="institution_profile_form__token"]')->extract(array('value'));
//         $csrf_token = $extract[0];
        
//         $profileFormValues =  array('institution_profile_form' => array('_token' => $csrf_token, 'description' => 'new edit description'));
//         $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $profileFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ), '{"description":"new edit description"}');
//         $this->assertRegExp('/institution/', $client->getResponse()->getContent());
//     }

//     public function testUpdateAddressForm()
//     {
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', $this->profileUri);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $extract = $crawler->filter('form#addressModalForm input[id="institution_profile_form__token"]')->extract(array('value'));
//         $csrf_token = $extract[0];

//         $profileFormValues =  array('institution_profile_form' => array('_token' => $csrf_token, 'address1' => array ( 'room_number' => 'test', 'building' => 'test', 'street' => 'test' ),
//                         'country' => '1','city' => '1','state' => '1','zipCode' => '232', 'addressHint'=> 'address Hint','coordinates'=> '10.689,122.9689'));
//         $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $profileFormValues);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
//         $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ), '{"address1":{"room_number":"test","building":"test","street":"Rosario Taytung Lacson"},
//                         "country" :"1","city":"1","zipCode":"232" }');
//         $this->assertRegExp('/institution/', $client->getResponse()->getContent());
        
//         //test empty form
//         $profileFormValues =  array('institution_profile_form' => array('_token' => $csrf_token, 'address1' => array ( 'room_number' => 'test', 'building' => 'test', 'street' =>  null ),
//                         'country' => null,'city' => null,'state' => null,'zipCode' => '23', 'addressHint'=> 'address Hint','coordinates'=> '10.689,122.9689'));
//         $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $profileFormValues);
//         $this->assertEquals(400, $client->getResponse()->getStatusCode());
//         $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ), '[{"field":"country","error":"Please provide your country."},
//         {"field":"city","error":"Please provide your city."},{"field":"zipCode","error":"Postal Code must be atleast 3 digits."},{"field":"address1","error":"Please provide a valid address."}]');
//         $this->assertRegExp('/errors/', $client->getResponse()->getContent());
        
//         //test invalid form
//         $profileFormValues =  array('institution_profile_form' => array('_token' => $csrf_token, 'address1' => array ( 'room_number' => 'test', 'building' => 'test', 'street' =>  null ),
//                         'country' => '4234','city' => '4234','state' => '43234','zipCode' => '23', 'addressHint'=> 'address Hint','coordinates'=> '10.689,122.9689'));
//         $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $profileFormValues);
//         $this->assertEquals(500, $client->getResponse()->getStatusCode());
//         $this->assertGreaterThan(0, $crawler->filter('html:contains("Cannot transform invalid country id ")')->count());
//     }
    public function testUpdateContactDetailsForm()
    {
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', $this->profileUri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('form#numberModalForm input[id="institution_profile_form__token"]')->extract(array('value'));
        $csrf_token = $extract[0];
        
        $profileFormValues =  array('institution_profile_form' => array('_token' => $csrf_token, 'websites' => "test.com",
                        'contactEmail' => 'tests@mail.com','contactDetails' => array ( '0' =>  array ( 'country_code' => '358', 'area_code' => '343','number' => '434','ext' => '3' ))));
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $profileFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->headers->contains( 'Content-Type','application/json' ), '{"websites":"test.com","contactEmail":"tests@mail.com","contactDetails":""}');
        $this->assertRegExp('/institution/', $client->getResponse()->getContent());
    }

    
//     public function testAjaxUpdateCoordinates(){
        
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $formValue =  array( 'coordinates' => '23423423423423');
//         $crawler = $client->request('POST', '/institution/ajax/update-coordinates', $formValue);
//         $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
//         $client = $this->getBrowserWithActualLoggedInUser();
//         $crawler = $client->request('GET', 'institution/ajax/update-coordinates');
//         $this->assertEquals(404, $client->getResponse()->getStatusCode());
//     }
    
}