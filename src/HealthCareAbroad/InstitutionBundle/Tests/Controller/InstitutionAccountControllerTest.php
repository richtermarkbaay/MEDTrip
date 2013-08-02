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
    private $profileFormValues =  array('institution_profile_form' => array(
        'name' => 'new nameasas',
        'address1' => array ( 'room_number' => 'test', 'building' => 'test', 'street' => 'test' ),
        'country' => '1',
        'city' => '1',
        'description' => 'test',
        'state' => '1',
        'zipCode' => '232',
        'contactEmail' => 'test@yahoo.com',
        'addressHint' => 'test',
        'medicalProviderGroups' => array( '0' => 'test'),
        'coordinates' => '10.3112791,123.89776089999998',
        'socialMediaSites' => array ( 'facebook' => 'test', 'twitter' => 'test','googleplus' => 'test' ),
//         'contactDetails' =>array ( '0' =>  array ( 'country_code' => '358', 'area_code' => '343','number' => '434','ext' => '3' )), //disabled as it cause error
        '_token' => '',
                    
    ));
    
    public function testSingleProfile()
    {
        $uri = '/institution/profile.html';
        
        $client = $this->getBrowserWithActualLoggedInUserForSingleType();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $invalidProfileFormValues =  array( 'institution_profile_form' => array( 'name' => null));
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $invalidProfileFormValues);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        $this->profileFormValues['institution_profile_form']['_token'] = $csrf_token;
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $this->profileFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $invalidAwardsFormValues =  array('institution_profile_form' => array( ));
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $invalidAwardsFormValues);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        
        /* With Services and test for empty medical providerss */
        $client = $this->getBrowserWithActualLoggedInUserForSingleType();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        $formServices['institution_profile_form']['medicalProviderGroups']= array( '0' => '');
        $formServices['institution_profile_form']['services']= array( 0 => '1');
        $formServices['institution_profile_form']['_token'] = $csrf_token;
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $formServices);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        /* End of services test */
        
        /* Test for awards */ 
        $client = $this->getBrowserWithActualLoggedInUserForSingleType();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        $formAwards = array('awardTypeKey' => 'award' , 'institution_profile_form' => array( 'awards' => array( 0 => '1') , '_token' => $csrf_token) );
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $formAwards);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        /* End of Awards Test */
        
        // test invalid institution
        $this->setInvalidInstitutionInSession($client);
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    private $multipleProfileFormValues =  array('institution_profile_form' => array(
        'address1' => array ( 'room_number' => 'test', 'building' => 'test', 'street' => 'test' ),
        'country' => '1',
        'city' => '1',
        'description' => 'test',
        'state' => '1',
        'zipCode' => '232',
        'contactEmail' => 'test@yahoo.com',
        'addressHint' => 'test',
        'medicalProviderGroups' => array( '0' => 'test'),
        'coordinates' => '10.3112791,123.89776089999998',
        'socialMediaSites' => array ( 'facebook' => 'test', 'twitter' => 'test','googleplus' => 'test' ),
        //         'contactDetails' =>array ( '0' =>  array ( 'country_code' => '358', 'area_code' => '343','number' => '434','ext' => '3' )),
        '_token' => '',
    
    ));
    
    public function testMultipleProfile()
    {
        $uri = '/institution/profile.html';
    
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $invalidProfileFormValues =  array( 'institution_profile_form' => array( 'name' => null));
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $invalidProfileFormValues);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        $this->multipleProfileFormValues['institution_profile_form']['_token'] = $csrf_token;
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $this->multipleProfileFormValues);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    
        $invalidAwardsFormValues =  array('institution_profile_form' => array( ));
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $invalidAwardsFormValues);
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    
        /* With Services and test for empty medical providerss */
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        $formServices['institution_profile_form']['medicalProviderGroups']= array( '0' => '');
        $formServices['institution_profile_form']['services']= array( 0 => '1');
        $formServices['institution_profile_form']['_token'] = $csrf_token;
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $formServices);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        /* End of services test */
    
        /* Test for awards */
        $client = $this->getBrowserWithActualLoggedInUserForMultitpleType();
        $crawler = $client->request('GET', $uri);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $extract = $crawler->filter('input[name="institution_profile_form[_token]"]')->extract(array('value'));
        $csrf_token = $extract[0];
        $formAwards = array('awardTypeKey' => 'award' , 'institution_profile_form' => array( 'awards' => array( 0 => '1') , '_token' => $csrf_token) );
        $crawler = $client->request('POST', '/institution/ajax/update-profile-by-field', $formAwards);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        /* End of Awards Test */
    
    }
    
    public function testAjaxUpdateCoordinates(){
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $formValue =  array( 'coordinates' => '23423423423423');
        $crawler = $client->request('POST', '/institution/ajax/update-coordinates', $formValue);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $client = $this->getBrowserWithActualLoggedInUser();
        $crawler = $client->request('GET', 'institution/ajax/update-coordinates');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
}