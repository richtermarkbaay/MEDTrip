<?php
/**
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\HelperBundle\Classes\CouchDatabase;
use HealthCareAbroad\HelperBundle\Listener\Alerts\AlertTypes;
use HealthCareAbroad\HelperBundle\Listener\Alerts\AlertClasses;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class AlertService
{
    const DATE_FORMAT = 'Y-m-d H:i:s';
    const ID_SEPARATOR = '.';
    const NULL_DATE = '1970-01-01 00:00:00';

    const ALL_ALERT_VIEW_URI = '_design/alerts/_view/all';
    const RECIPIENT_ALERT_VIEW_URI = '_design/alerts/_view/recipient';
    const REFERENCE_ALERT_VIEW_URI = '_design/alerts/_view/reference';
    const TYPE_AND_REFERENCE_ALERT_VIEW_URI = '_design/alerts/_view/typeAndReference';


	protected $doctrine;
	protected $container;
	protected $couchDb;

	function __construct($doctrine, $container) 
	{
	    $this->doctrine = $doctrine;
	    $this->container = $container;

	    $alertCouchDb = $this->container->getParameter('alert_db');
	    $this->couchDB = new CouchDatabase($alertCouchDb['host'], $alertCouchDb['port'], $alertCouchDb['database']);
	}

	/**
	 * 
	 * @return array
	 */
// 	function getAlerts()
// 	{
// 	    $options['dateAlert'] = array('operator' => '<=', 'value' => date(self::DATE_FORMAT));
//         $alerts = $this->couchDB->getBy($options);
//         $alertData = $this->formatAlertData($alerts);

//         return $alertData;
// 	}
	
	/**
	 * 
	 * @param Institution $institution
	 * @return array
	 */
	function getAlertsByInstitution(Institution $institution)
	{
        $params = array(
            'keys' => array(
                array($institution->getId(), AlertRecipient::INSTITUTION), 
                array(null, AlertRecipient::ALL_ACTIVE_INSTITUTION)
            )
        );

        return $this->getAlerts(self::RECIPIENT_ALERT_VIEW_URI, $params, true);
	}

    /**
     * 
     * @param int $accountId
     * @return \HealthCareAbroad\HelperBundle\Services\Ambigous
     */
	function getAdminAlerts($accountId = null)
	{
        $params = array(
            'keys' => array(
                array(null, AlertRecipient::ALL_ACTIVE_ADMIN), 
                array((int)$accountId, AlertRecipient::ADMIN)
            )
        );

        return $this->getAlerts(self::RECIPIENT_ALERT_VIEW_URI, $params, true);
	}


	/**
	 * 
	 * @param string $uri
	 * @param array $params
	 * @param bool $groupByType
	 * @return Ambigous <\HealthCareAbroad\HelperBundle\Services\Ambigous, unknown, multitype:unknown >
	 */
	function getAlerts($uri = self::ALL_ALERT_VIEW_URI, $params = array(), $groupByType = false)
	{
	    $result = $this->couchDB->getView($uri, $params);

	    return $this->formatAlertData($result, $groupByType);
	}

	/**
	 * 
	 * @param unknown_type $id
	 * @return Ambigous <NULL, mixed>
	 */
	function getAlert($id)
	{
	    $alert = json_decode($this->couchDB->get($id), true);

	    if(isset($alert['error'])) {
	        $alert = null;
	    }

	    return $alert;
	}

	/**
	 * 
	 * @param mixed $data
	 * @return Ambigous <multitype:, unknown>
	 */
	function formatAlertData($data, $groupByType = false)
	{
	    $formattedData = array();

	    if(!is_array($data))
	        $data = json_decode($data, true);

	    if(isset($data['total_rows']) && $data['total_rows'] > 0) {
	    
    	    if(!$groupByType) {
    	        foreach($data['rows'] as $each) {
    	            $alert = $this->formatAlert($each['value']);
    	            $formattedData[] = $alert;
    	        }
    	    } else {
	            foreach($data['rows'] as $each) {
	                $alert = $this->formatAlert($each['value']);
	                $formattedData[$alert['type']][] = $alert;
	            }
    	    }
	    }

	    return $formattedData;
	}
	
	function formatAlert($data = array())
	{
	    return $data;
	}

    /**
     * 
     * @param array $arrayData
     */
    function save($alertData = array())
    {
        $data = $this->validateData($alertData);

        if(!isset($data['_id']) || !$data['_id']) {
            $id = $this->generateAlertId();
        } else {
            $id = $data['_id'];
            $alert = $this->getAlert($id);
            $data['_rev'] = $alert['_rev'];
        }

        return $this->couchDB->put($id, $data);
    }

    /**
     *
     * @param array $arrayData
     */
    function multipleUpdate(array $arrayData = array())
    {
        $start = (float)microtime();
    
        if(!count($arrayData)) {
            return;
        }
    
        foreach($arrayData as $key => $data) {

            $data = $this->validateData($data);

            if(isset($data['_id']) && $alert = $this->getAlert($data['_id'])) {
                $arrayData[$key]['_rev'] = $alert['_rev'];
            } else {
                $arrayData[$key]['_id'] = $this->generateAlertId();
            }
        }
    
        $result = $this->couchDB->multipleUpdate($arrayData);

        $end = (float)microtime();
        $time = $end - $start;
        //var_dump("processTime: " . $time);
        
        return $result;
    }

    /**
     * 
     * @param string $id
     * @param string $rev
     * @return mixed
     */
    function delete($id, $rev)
    {
        return $this->couchDB->delete($id, $rev);
    }

    function generateAlertId()
    {
        $time = microtime();

        return md5($time);
    }

    
    /**
     * 
     * @param array $data
     * @return string
     */
    function validateData($data = array())
    {
        if(!isset($data['referenceData']) || !isset($data['referenceData']['id']) || !isset($data['class']) || !AlertClasses::isValidClass($data['class'])) {
            throw new \ErrorException('Invalid Alert Data!');
        }

        if(!isset($data['message']) || $data['message'] == '')
            $data['message'] = '';

        if(!isset($data['type']) || $data['type'] == '') {
            $data['type'] = AlertTypes::DEFAULT_TYPE;
        }

        if(!isset($data['dateAlert'])) {
            $data['dateAlert'] = date(self::DATE_FORMAT);
        }

        $data['dateCreated'] = date(self::DATE_FORMAT);

        return $data;
    }
}