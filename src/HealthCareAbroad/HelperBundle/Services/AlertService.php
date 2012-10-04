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
    const ADMIN_RECIPIENT = 'Admin';

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
	function getAlerts()
	{
	    $options['dateAlert'] = array('operator' => '<=', 'value' => date(self::DATE_FORMAT));
        $alerts = $this->couchDB->getBy($options);
        $alertData = $this->formatAlertData($alerts);

        return $alertData;
	}
	
	/**
	 * 
	 * @param Institution $institution
	 * @return array
	 */
	function getAlertsByInstitution(Institution $institution)
	{
	    $alerts = array();
	    $options = array(
            'institutionId' => $institution->getId(),
            'dateAlert' => array('operator' => '<=', 'value' => date(self::DATE_FORMAT))
        );

	    $result = $this->couchDB->getBy($options);

	    return $this->formatAlertData($result);
	}
	
	/**
	 *
	 * @return array
	 */
	function getAdminAlerts()
	{
	    $alerts = array();
	    $options = array(
            'recipient' => self::ADMIN_RECIPIENT,
            'dateAlert' => array('operator' => '<=', 'value' => date(self::DATE_FORMAT))
	    );
	
	    $result = $this->couchDB->getBy($options);

	    return $this->formatAlertData($result);
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
	function formatAlertData($data)
	{
	    if(!is_array($data))
	        $data = json_decode($data, true);

	    $formattedData = array();
	    foreach($data['rows'] as $each) {
	        $alert = $this->formatAlert($each['value']);
	        $formattedData[$alert['type']][] = $alert;
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
    function save(array $arrayData = array())
    {
        $start = (float)microtime();

        if(!count($arrayData)) {
            return;
        }

        foreach($arrayData as $key => $data) {

            $data = $this->validateData($data);

            $id = $this->generateAlertId($data['referenceData']['id'], $data['class'], $data['type']);
            $alert = $this->getAlert($id);
            $arrayData[$key]['_id'] = $id;

            if($alert) {
                $arrayData[$key]['_rev'] = $alert['_rev'];
            }
        }

        $result = $this->couchDB->multipleUpdate($arrayData);

        $end = (float)microtime();
        $time = $end - $start;
        //var_dump("processTime: " . $time);
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
     * @param int $referenceId
     * @param string $class
     * @param string $type
     * @throws \ErrorException
     * @return string md5($id)
     */
    function generateAlertId($referenceId = null, $class = null, $type = null)
    {
        if(!$referenceId || !$class || !$type) {
            throw new \ErrorException('Unable to generate Alert Id! Invalid parameter(s).');
        }

        $id = $referenceId . self::ID_SEPARATOR . $class . self::ID_SEPARATOR  . $type;

        return md5($id);
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