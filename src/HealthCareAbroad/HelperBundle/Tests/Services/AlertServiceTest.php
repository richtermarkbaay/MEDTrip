<?php
/**
 * Unit test for TokenService
 * 
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\HelperBundle\Tests\Services;


use HealthCareAbroad\HelperBundle\Tests\HelperBundleTestCase;

use HealthCareAbroad\HelperBundle\Services\AlertService;
use HealthCareAbroad\HelperBundle\Services\AlertRecipient;

use HealthCareAbroad\HelperBundle\Listener\Alerts\AlertTypes;
use HealthCareAbroad\HelperBundle\Listener\Alerts\AlertClasses;



class AlertServiceTest extends HelperBundleTestCase
{
	/**
	 *
	 * @var HealthCareAbroad\HelperBundle\Services\AlertService
	 */
	protected $service;
	
	public function setUp()
	{
		$this->service = $this->getServiceContainer()->get('services.alert');
	}
	
	public function tearDown()
	{
		$this->service = null;
	}

	public function testCreateAlertForSpecificInstitution()
	{
	    $referenceData = array('id' => 1, 'name' => 'Test Reference Data name.');

	    $data = array(
            'recipient' => 1,
            'recipientType' => AlertRecipient::INSTITUTION,
            'referenceData' => $referenceData,
            'message' => 'this is a test',
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::DRAFT_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
	    );

	    $result = $this->service->save($data);

	    $this->assertEquals(true, $result['ok']);
	    return $result['id'];
	}

	public function testCreateAlertForAllActiveAdmin()
	{
	    $referenceData = array('id' => 1, 'name' => 'Test Reference Data name.');
	
	    $data = array(
            'recipient' => null,
            'recipientType' => AlertRecipient::ALL_ACTIVE_ADMIN,
            'referenceData' => $referenceData,
            'message' => 'this is an alert for all active admin',
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::PENDING_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
	    );

	    $result = $this->service->save($data);
    
        $this->assertEquals(true, $result['ok']);
        return $result['id'];
	}

	public function testCreateInvalidAlertData()
	{

	    $referenceData = array('id' => 1, 'name' => 'Test Reference Data name.');
	
	    $data = array(
            'recipient' => null,
            'recipientType' => AlertRecipient::ALL_ACTIVE_ADMIN,
            'referenceData' => $referenceData,
            'message' => 'this is an invalid alert',
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => 'invalidType',
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
	    );

        // Test for Invalid alert type
	    try {
	        $result = $this->service->save($data);
	        $result = $result['ok'];
	    }catch(\Exception $e) {
	        $result = false;
	    }
	    $this->assertEquals(false, $result, 'Alert should not be created when type is invalid. Given type is ' . $data['type']);
	    

	    
	    // Test for Empty ReferenceData
	    try {
	        $data['type'] = AlertTypes::NEW_INSTITUTION;
	        unset($data['referenceData']);
	        $result = $this->service->save($data);
	        $result = $result['ok'];
	    }catch(\Exception $e) {
	        $result = false;
	    }
	    $this->assertEquals(false, $result, 'Alert should not be created when referenceData is not defined!');
	}
	
	public function testCreateAndUpdate()
	{
	    $referenceData = array('id' => 1, 'name' => 'Test with createUpdate.');
	
	    $data = array(
            'recipient' => 1,
            'recipientType' => AlertRecipient::INSTITUTION,
            'referenceData' => $referenceData,
            'message' => 'this is created in testCreateAndUpdate',
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::DENIED_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
	    );
	
	    $result = $this->service->save($data);

	    $data['_id'] = $result['id'];
	    $data['_rev'] = $result['rev'];
	    $data['referenceData']['name'] .= ' Updated';
        $result = $this->service->save($data);
        $this->assertEquals(true, $result['ok']);
        
        // Update Without Rev
        unset($data['_rev']);
        $result1 = $this->service->save($data);
        $this->assertEquals($result['id'], $result1['id']);        

        
        // Update With Invalid Id
        try {
            $data['_id'] = '12324234324234';
            $result1 = $this->service->save($data);
            $this->assertTrue(true, 'Updating Alert with invalid ID should not be permitted!');
        } catch(\ErrorException $e) {}
	}

	public function testMultipleUpdate()
	{
	    // Multiple Update with empty $data
	    $data = array();
	    $result = $this->service->multipleUpdate($data);
	    $this->assertEquals(null, $result, 'Multiple update with empty data should return null!');


	    // Update With Invalid referenceData
	    $invalidData[] = array(
            'recipient' => null,
            'recipientType' => AlertRecipient::ALL_ACTIVE_ADMIN,
            'referenceData' => array(),
            'message' => 'this is created in testMultipleUpdate',
            'class' => AlertClasses::INSTITUTION,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
	    );
	    try {
	        $result = $this->service->multipleUpdate($invalidData);
	        $this->assertTrue(true, 'Updating with invalid referenceData should not be permitted!');
        } catch(\ErrorException $e) {}
	    
	    
        // Update With Invalid alert type
        $invalidType[] = array(
            'type' => 100,
            'recipient' => null,
            'recipientType' => AlertRecipient::ALL_ACTIVE_ADMIN,
            'referenceData' => array('id' => 2, 'name' => 'test etst.'),
            'message' => 'this is created in testMultipleUpdate',
            'class' => AlertClasses::INSTITUTION,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
        );
        try {
            $result = $this->service->multipleUpdate($invalidType);
            $this->assertTrue(false, 'Updating with invalid alert type should not be permitted!');
        } catch(\ErrorException $e) {}
        

        
        // Update With Invalid recipientType
        $invalidRecipientType[] = array(
            'type' => AlertTypes::DRAFT_LISTING,
            'recipient' => 1,
            'referenceData' => array('id' => 2, 'name' => 'test etst.'),
            'message' => 'this is created in testMultipleUpdate',
            'class' => AlertClasses::INSTITUTION,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
        );
        try {
            $result = $this->service->multipleUpdate($invalidRecipientType);
            $this->assertTrue(false, 'Updating with invalid alert recipientType should not be permitted!');
        } catch(\ErrorException $e) {}


        // Update With Invalid class
        $invalidClass[] = array(
            'type' => AlertTypes::DRAFT_LISTING,
            'recipient' => 1,
            'recipientType' => AlertRecipient::INSTITUTION,
            'referenceData' => array('id' => 2, 'name' => 'test etst.'),
            'message' => 'this is created in testMultipleUpdate',
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
        );
        try {
            $result = $this->service->multipleUpdate($invalidClass);
            $this->assertTrue(false, 'Updating with invalid alert class should not be permitted!');
        } catch(\ErrorException $e) {}
        
        
        // Vaild Alert data
	    $referenceData = array('id' => 1, 'name' => 'Test Reference Data name2.');
	    $data[] = array(
            'recipient' => null,
            'recipientType' => AlertRecipient::ALL_ACTIVE_ADMIN,
            'referenceData' => $referenceData,
            'message' => 'this is created in testMultipleUpdate',
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::EXPIRED_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
	    );

	    $referenceData['name'] = 'Test Reference Data deleted.';
	    $data[] = array(
            'recipient' => 1,
            'recipientType' => AlertRecipient::INSTITUTION,
            'referenceData' => $referenceData,
            'message' => 'this is created in testMultipleUpdate',
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::EXPIRED_LISTING,
            'isDeletable' => true,
	    );

	    $result = $this->service->multipleUpdate($data);
	    $this->assertEquals(true, $result[0]['ok']);
	    
	    
	    // Delete alert
	    $delResult = $this->service->delete($result[0]['id'],$result[0]['rev']);
	    $this->assertEquals(true, $delResult['ok'], 'Failed deleting alert with id ' . $result[0]['id'] . ' and rev ' . $result[0]['rev']);        
	}

	public function testGetAlertsByInstitution()
	{
        $institution = $this->getServiceContainer()->get('doctrine')->getRepository('InstitutionBundle:Institution')->find(1);
        $alerts = $this->service->getAlertsByInstitution($institution);

        $this->assertGreaterThan(0, count($alerts));
	}


	public function testGetAdminAlerts()
	{
	    $alerts = $this->service->getAdminAlerts(1);

	    $this->assertGreaterThan(0, count($alerts));
	}

	public function testGetAlerts()
	{
	    $alerts = $this->service->getAlerts();
	    $this->assertGreaterThan(0, count($alerts));
	}

	public function testGetAlert()
	{
	    $referenceData = array('id' => 1, 'name' => 'Test Reference Data name on GetAlert.');
	
	    $data = array(
            'recipient' => 1,
            'recipientType' => AlertRecipient::ALL_ACTIVE_ADMIN,
            'referenceData' => $referenceData,
            'message' => 'created from testGetAlert',
            'class' => AlertClasses::INSTITUTION,
            'type' => AlertTypes::NEW_INSTITUTION,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
	    );

	    $result = $this->service->save($data);
	    
	    $alert = $this->service->getAlert($result['id']);

	    $this->assertEquals(true, isset($alert['_id']));
	}

	public function testGetAlertWithInvalidId()
	{
	    try {
	        $alert = (bool)$this->service->getAlert(1234);
	        
	        $this->assertEquals(false, $alert);
	    } catch(\ErrorException $e) {}
	}
	
	public function testGetAlertWithNullId()
	{
        $alert = $this->service->getAlert(null);
        $this->assertEquals(null, null);
	}
}