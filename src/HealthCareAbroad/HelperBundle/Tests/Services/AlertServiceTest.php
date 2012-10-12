<?php
/**
 * Unit test for TokenService
 * 
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\HelperBundle\Tests\Services;


use HealthCareAbroad\HelperBundle\Listener\Alerts\AlertTypes;

use HealthCareAbroad\HelperBundle\Services\AlertRecipient;

use HealthCareAbroad\HelperBundle\Listener\Alerts\AlertClasses;

use HealthCareAbroad\HelperBundle\Tests\HelperBundleTestCase;

use HealthCareAbroad\HelperBundle\Services\AlertService;

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
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::PENDING_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true,
	    );

	    $result = $this->service->save($data);
    
        $this->assertEquals(true, $result['ok']);
        return $result['id'];
	}

	// TODO - Make this work!
	public function testGetAlertsByInstitution()
	{
        $institution = $this->getServiceContainer()->get('doctrine')->getRepository('InstitutionBundle:Institution')->find(1);
        $alerts = $this->service->getAlertsByInstitution($institution);

        //$this->assertGreaterThan(0, count($alerts));
	}

	// TODO - Make this work!
	public function testGetAdminAlerts()
	{
	    $alerts = $this->service->getAdminAlerts(1);
	    //$this->assertGreaterThan(0, count($alerts));
	}
	
	public function testGetAlerts()
	{
	    $alerts = $this->service->getAlerts();
	    $this->assertGreaterThan(0, count($alerts));
	}

	public function testGetAlert()
	{
	    $alertId = $this->testCreateAlertForAllActiveAdmin();

	    $alert = $this->service->getAlert($alertId);

	    $this->assertEquals(true, isset($alert['_id']));
	
	    return $alert;
	}

	public function multipleUpdate()
	{
	    
	}
	
	public function delete()
	{
	     
	}
}