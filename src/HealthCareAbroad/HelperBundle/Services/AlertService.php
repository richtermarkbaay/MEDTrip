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
	private function formatAlertData($data, $groupByType = false)
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

	/**
	 * TODO - Format Alert 
	 * @param unknown_type $data
	 * @return unknown
	 */
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

    /**
     * 
     * @return string genrated based on current microtime and encoded to md5()
     */
    function generateAlertId()
    {
        $time = microtime();

        return md5($time);
    }

    
    /**
     * TODO - Need to finalize validation Rules!
     * @param array $data
     * @return string
     */
    function validateData($data = array())
    {
        if(!isset($data['type'])) {
            $data['type'] = AlertTypes::DEFAULT_TYPE;
        }

        if(!isset($data['referenceData']) || !isset($data['referenceData']['id'])) {
            throw new \ErrorException('Invalid Alert Data!' . json_encode($data));
        }
        
        if(!AlertTypes::isValid($data['type'])) {
            $message = 'Invalid alert type ' . $data['type'] . '. Valid values are: [' . implode(', ',array_values(AlertTypes::getAll())) . ']';
            throw new \ErrorException($message);
        }
        
        if(!isset($data['recipientType']) || !AlertRecipient::isValid($data['recipientType'])) {
            $message = 'Invalid recipientType ' . $data['recipientType'] . '. Valid values are: [' . implode(', ',array_values(AlertRecipient::getAll())) . ']';
            throw new \ErrorException($message);
        }

        if(!isset($data['class']) || !AlertClasses::isValidClass($data['class'])) {
            $message = 'Invalid class value ' . $data['class'] . '. Valid values are: [' . implode(', ',array_values(AlertClasses::getClasses())) . ']';
            throw new \ErrorException($message);            
        }


        if(!isset($data['message']) || $data['message'] == '')
            $data['message'] = '';

        if(!isset($data['dateAlert'])) {
            $data['dateAlert'] = date(self::DATE_FORMAT);
        }

        $data['dateCreated'] = date(self::DATE_FORMAT);

        return $data;
    }
}