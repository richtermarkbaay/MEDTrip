<?php

namespace HealthCareAbroad\HelperBundle\Services;

use Doctrine\ORM\Query;

use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\State;
use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\HelperBundle\Exception\LocationServiceException;

class LocationService
{
	protected $doctrine;

	/**
	 * @var ChromediaGlobalRequest
	 */
	private $request;

	private $loadedGlobalCountryList = array();

	private $totalResults;

	private static $activeCountries = null;

	public $chromediaApiUri;

	/**
	 * 
	 * @var CouchDbService
	 */
	public $couchDbServie;

	/**
	 * @var LocationService
	 */
	private static $instance = null;

	public function __construct()
	{
	    static::$instance = $this;
	}

	public function setChromediaApiUri($uri)
	{
	    $this->chromediaApiUri = $uri;
	}

	public function setChromediaGlobalRequest(ChromediaGlobalRequest $request)
	{
	    $this->request = $request;
	}

	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}
	
	public function setCouchDbService(CouchDbService $couchDbService, $locationDb)
	{
	    $this->couchDbService = $couchDbService;
	    $this->couchDbService->setDatabase($locationDb);
	}

	/**
	 * Hackish way to use this service without injecting it on other services.
	 *
	 * @return \HealthCareAbroad\HelperBundle\Services\LocationService
	 */
	public static function getCurrentInstance()
	{
	    return self::$instance;
	}

    /// ======== Country Related Functions ======== //
	/**
	 * Currently being used in Institution Signup Only
	 * 
	 * @param array $data
	 * @param unknown_type $id
	 * @return \HealthCareAbroad\HelperBundle\Entity\Country or NULL
	 */
	public function updateCountry(array $data, $id)
	{
	    if($countryObj = $this->getCountryById($id)) {
	        $em = $this->doctrine->getEntityManagerForClass('HelperBundle:Country');

	        $countryObj->setName($data['name']);
	        $countryObj->setCcIso($data['ccIso']);
	        $countryObj->setCountryCode($data['countryCode']);
	        $countryObj->setSlug($data['slug']);
	        $countryObj->setStatus($data['status']);
	
	        $em->persist($countryObj);
	        $em->flush();
	         
	        return $countryObj;
	    }

	    return null;
	}

	/**
	 * Add Gobal GeoCountry
	 * @param array $countryData
	 * @return mixed
	 */
	public function addGlobalCountry(array $countryData)
	{
	    $response = $this->request->post($this->chromediaApiUri.'/countries', array('geoCountry' => $countryData));	    
	
	    return \json_decode($response->getBody(true), true);
	}

	/**
	 * Update Global GeoCountry
	 * @param array $countryData
	 * @param unknown_type $id
	 * @return mixed
	 */
	public function updateGlobalCountry(array $countryData, $id)
	{
	    $response = $this->request->post($this->chromediaApiUri. "/countries/$id", array('geoCountry' => $countryData));

	    if (200 != $response->getStatusCode()) {
	        throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getReasonPhrase());
	    }
	
	    return \json_decode($response->getBody(true), true);
	}
	
	/**
	 * Update Global GeoCountry status
	 * @param unknown_type $id
	 * @param unknown_type $status
	 * @return mixed
	 */
	public function updateGlobalCountryStatus($id, $status)
	{
	    $response = $this->request->post($this->chromediaApiUri."/countries/$id/update-status", array('status' => $status));

	    return \json_decode($response->getBody(true), true);
	}

	/**
	 * Get Global GeoCountry
	 * @param unknown_type $id
	 * @return mixed
	 */
	public function getGlobalCountryById($id)
	{
	    $response = $this->request->get($this->chromediaApiUri.'/countries/'.$id);
	    if (200 != $response->getStatusCode()) {
	        throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
	    }
	
	    return \json_decode($response->getBody(true), true);
	}

    /**
     * Get List of Global GeoCountry with parameters
     * @param array $params
     * @return Ambigous <multitype:, mixed>
     */
	public function getGlobalCountries(array $params = array())
	{
	    static $hasLoaded = false;
	    static $results = array();
	     
	    if (!$hasLoaded) {
	        $queryString = count($params) ? '?' . http_build_query($params) : '';
	        $response = $this->request->get($this->chromediaApiUri."/countries" . $queryString);
	
	        if (200 != $response->getStatusCode()) {
	            throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
	        }
	         
	        $results = \json_decode($response->getBody(true), true);
	
	        $hasLoaded = true;
	    }
	     
	    return $results;
	}

	/**
	 * Get Local Country
	 * @param unknown_type $id
	 * @return \HealthCareAbroad\HelperBundle\Entity\Country or NULL
	 */
	public function getCountryById($id)
	{
	    return $this->doctrine->getRepository('HelperBundle:Country')->find($id);
	}

	/**
	 * Get Local Country
	 * @param string $slug
	 * @return \HealthCareAbroad\HelperBundle\Entity\Country or NULL
	 */
	public function getCountryBySlug($slug)
	{
	    static $countryBySlugs = array();
	    if (!isset($countryBySlugs[$slug])) {
	        $countryBySlugs[$slug] = $this->doctrine->getRepository('HelperBundle:Country')->findOneBySlug($slug);
	    }
	     
	    return $countryBySlugs[$slug];
	}

	/**
	 * Create Country instance from array $data
	 *
	 * @param array $data
	 * @return \HealthCareAbroad\HelperBundle\Entity\Country
	 */
	public function createCountryFromArray(array $data)
	{
	    $requiredFields = array('id', 'name', 'slug');
	     
	    foreach ($requiredFields as $key) {
	        if (!isset($data[$key])) {
	            throw LocationServiceException::missingRequiredCountryDataKey($key);
	        }
	    }

	    $country = new Country();
	    $country->setId($data['id']);
	    $country->setName($data['name']);
	    $country->setSlug($data['slug']);
	    $country->setCcIso(isset($data['ccIso']) ? $data['ccIso'] : '');
	    $country->setCountryCode(isset($data['countryCode']) ? $data['countryCode'] : '');
	    $country->setStatus(isset($data['code']) ? $data['code'] : Country::STATUS_ACTIVE);

	    return $country;
	}

	/**
	 * Get Active Local Country
	 * @param unknown_type $hydrationMode
	 * @return array or NULL
	 */
	public function getActiveCountries($hydrationMode=Query::HYDRATE_ARRAY)
	{
	    if(!static::$activeCountries) {
	        $qb = $this->getQueryBuilderForCountries();
	        static::$activeCountries = $qb->getQuery()->getResult($hydrationMode);	        
	    }

	    return static::$activeCountries;
	}

	public function getQueryBuilderForCountries()
	{
	    return $this->doctrine->getEntityManager()->getRepository('HelperBundle:Country')->getQueryBuilderForCountries();
	}
	/// ======== End of Country Related Functions ======== //



    /// ======== State Related Functions ======== //
	/**
	 * Currently being used in Institution Signup Only
	 * 
	 * This will add a new data in global states (geo_states) first
	 * and will save/update local states if success.
	 * @param array $data
	 * @return \HealthCareAbroad\HelperBundle\Entity\State
	 */
	public function addNewState(array $data) {
	    // Save to global state. 
	    $stateData = $this->addGlobalState($data);

        if(isset($stateData['form'])) {
            return null;
        }

	    // Update local state if successfully saved in global state.
	    $state = $this->getStateById($stateData['id']);
	    if(!$state) {
	        $state = new State();
	        $state->setId($stateData['id']);
	        $state->setCountry($this->getCountryById($data['geoCountry']));
	    }

	    $state->setName($stateData['name']);
	    $state->setInstitutionId($stateData['institutionId']);
	    $state->setStatus($stateData['status']);

	    $em = $this->doctrine->getEntityManagerForClass('HelperBundle:State');
	    $em->persist($state);
	    $em->flush($state);

	    return $state;
	}

	/**
	 * Update Local State
	 * 
	 * Note: Currently being used in Institution Signup Only 
	 * @param array $data
	 * @param unknown_type $id
	 */
	public function updateState(array $data, $id)
	{
	    if($stateObj = $this->getStateById($id)) {
	        $em = $this->doctrine->getEntityManagerForClass('HelperBundle:State');
	        $country = $this->getCountryById($data['geoCountry']['id']);

	        if(!$country) {
	            $country = $this->createCountryFromArray($data['geoCountry']);
	            $em->persist($country);
	        }

	        $stateObj->setName($data['name']);
	        $stateObj->setCountry($country);
	        $state->setAdministrativeCode(isset($data['administrativeCode']) ? $data['administrativeCode'] : null);
	        //$stateObj->setSlug($data['slug']);
	        $stateObj->setStatus($data['status']);

	        $em->persist($stateObj);
	        $em->flush();
	    }
	}

	/**
	 * Add Global State
	 * 
	 * @param array $stateData
	 * @return mixed
	 */
	public function addGlobalState(array $stateData)
	{
	    if(!isset($stateData['institutionId']) || is_null($stateData['institutionId']))
	        $stateData['institutionId'] = 0;

	    $response = $this->request->post($this->chromediaApiUri.'/states', array('geoState' => $stateData));

	    return \json_decode($response->getBody(true), true);
	}
	
	/**
	 * Update Global State
	 * 
	 * @param array $stateData
	 * @param unknown_type $id
	 * @return mixed
	 */
	public function updateGlobalState(array $stateData, $id)
	{
	    $response = $this->request->post($this->chromediaApiUri."/states/$id", array('geoState' => $stateData));
	
	    return \json_decode($response->getBody(true), true);
	}

	/**
	 * Update Global State status
	 * 
	 * @param unknown_type $id
	 * @param unknown_type $status
	 * @return mixed
	 */
	public function updateGlobalStateStatus($id, $status)
	{
	    $response = $this->request->post($this->chromediaApiUri. "/states/$id/update-status", array('status' => $status));
	
	    if (200 != $response->getStatusCode()) {
	        throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getReasonPhrase());
	    }
	
	    return \json_decode($response->getBody(true), true);
	}

	/**
	 * Get Lost of Global State with parameters
	 * 
	 * @param array $params
	 * @return mixed
	 */
	public function getGlobalStates(array $params)
	{
	    static $hasLoaded = false;
	
	    if (!$hasLoaded) {
	        $queryString = count($params) ? '?' . http_build_query($params) : '';
	        $response = $this->request->get($this->chromediaApiUri."/states" . $queryString);

	        if (200 != $response->getStatusCode()) {
	            throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
	        }

	        $results = \json_decode($response->getBody(true), true);

	        $hasLoaded = true;
	    }

	    return $results;
	}

	/**
	 * Get Global State
	 * 
	 * @param unknown_type $id
	 * @return NULL|mixed
	 */
	public function getGlobalStateById($id)
	{
	    $response = $this->request->get($this->chromediaApiUri.'/states/'.$id);

	    if (200 != $response->getStatusCode()) {
	        if (404 == $response->getStatusCode()){
	            return null; // id does not exist
	        }
	        else {
	            throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getReasonPhrase());
	        }
	    }

	    return \json_decode($response->getBody(true), true);
	}

	/**
	 * Get Local State
	 * 
	 * @param unknown_type $id
	 */
	public function getStateById($id)
	{
	    return $this->doctrine->getRepository('HelperBundle:State')->find($id);
	}

	/**
	 * Create State instance from array $data
	 *
	 * @param array $data
	 * @return \HealthCareAbroad\HelperBundle\Entity\State
	 */
	public function createStateFromArray(array $data)
	{
	    $requiredFields = array('id', 'name', 'country');
	
	    if(isset($data['geoCountry']) && isset($data['geoCountry']['id'])) {
	        $data['country'] = $this->getCountryById($data['geoCountry']['id']);
	    }
	
	    foreach ($requiredFields as $key) {
	        if (!isset($data[$key])) {
	            throw LocationServiceException::missingRequiredStateDataKey($key);
	        }
	    }
	
	    $state = new State();
	    $state->setId($data['id']);
	    $state->setName($data['name']);
	    $state->setCountry($data['country']);
	    $state->setInstitutionId(isset($data['institutionId']) ? $data['institutionId'] : 0);
	    $state->setAdministrativeCode(isset($data['administrativeCode']) ? $data['administrativeCode'] : null);
	    $state->setStatus(isset($data['status']) ? $data['status'] : State::STATUS_ACTIVE);
	
	    return $state;
	}
	/// ======== End of State Related Functions ======== //


    /// ======== City Related Functions ======== //
	/** 
	 * Add Local City
	 * 
 	 * Note: Currently being used in Institution Signup Only
 	 * 
	 * @param array $data
	 * @return \HealthCareAbroad\HelperBundle\Entity\City
	 */
	public function addNewCity(array $data) {
	    // Save to global city.
	    $cityData = $this->addGlobalCity($data);

	    if(isset($cityData['form'])) {
	        return null;
	    }

	    $city = $this->getCityById($cityData['id']);

	    if(!$city) {
	        $city = new City();
	        $city->setId($cityData['id']);	
	        $city->setCountry($this->getCountryById($data['geoCountry']));
	    }

	    if($data['geoState']) {
	        $state = $this->getStateById($data['geoState']);
	        $city->setState($state ? $state : null);
	    }

	    $city->setName($cityData['name']);
	    $city->setSlug($cityData['slug']);
	    $city->setInstitutionId($cityData['institutionId']);
	    $city->setStatus($cityData['status']);

	    $em = $this->doctrine->getEntityManagerForClass('HelperBundle:City');
	    $em->persist($city);
	    $em->flush($city);

	    return $city;
	}

	/**
	 * Update Local City
	 * 
	 * Note: Currently being used in Institution Signup Only
	 * 
	 * @param array $data
	 * @param $id
	 * @return \HealthCareAbroad\HelperBundle\Entity\City|NULL
	 */
	public function updateCity(array $data, $id)
	{
	    if($cityObj = $this->getCityById($id)) {
	        $em = $this->doctrine->getEntityManagerForClass('HelperBundle:City');
	        $country = $this->getCountryById($data['geoCountry']['id']);

	        if(!$country) {
	            $country = $this->createCountryFromArray($data['geoCountry']);
	            $em->persist($country);
	        }
	
	        if(!isset($data['geoState']) || !$data['geoState']) {
	            $cityObj->setState(null);
	        } else {
	            $state = $this->getStateById($data['geoState']['id']);
	            if(!$state) {
	                $data['geoState']['country'] = $country;
	                $state = $this->createStateFromArray($data['geoState']);
	            }

	            $em->persist($state);
	        }

	        $cityObj->setName($data['name']);
	        $cityObj->setCountry($country);
	        $cityObj->setState($state);
	        $cityObj->setSlug($data['slug']);
	        $cityObj->setStatus($data['status']);

	        $em->persist($cityObj);
	        $em->flush();

	        return $cityObj;
	    }

	    return null;
	}

	/** 
	 * Add Global City
	 * 
	 * @param array $cityData
	 * @return mixed
	 */
	public function addGlobalCity(array $cityData)
	{
	    if(!isset($cityData['institutionId']) || is_null($cityData['institutionId'])) 
	        $cityData['institutionId'] = 0;

	    $response = $this->request->post($this->chromediaApiUri.'/cities', array('geoCity' => $cityData));

	    return \json_decode($response->getBody(true), true);
	}

	/** 
	 * Update Global City
	 * 
	 * @param array $cityData
	 * @param $id
	 * @return mixed
	 */
	public function updateGlobalCity(array $cityData, $id)
	{
	    $response = $this->request->post($this->chromediaApiUri."/cities/$id", array('geoCity' => $cityData));

	    return \json_decode($response->getBody(true), true);
	}

	/**
	 * Update Global City status
	 * 
	 * @param unknown_type $id
	 * @param unknown_type $status
	 * @return mixed
	 */
	public function updateGlobalCityStatus($id, $status)
	{
	    $response = $this->request->post($this->chromediaApiUri. "/cities/$id/update-status", array('status' => $status));

	    if (200 != $response->getStatusCode()) {
	        throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getReasonPhrase());
	    }

	    return \json_decode($response->getBody(true), true);
	}

	/**
	 * Get List of Global City with parameters
	 * 
	 * @param array $params
	 * @return mixed
	 */
	public function getGlobalCities(array $params)
	{
	    static $hasLoaded = false;

	    if (!$hasLoaded) {
	        $queryString = count($params) ? '?' . http_build_query($params) : '';

	        $response = $this->request->get($this->chromediaApiUri."/cities" . $queryString);
	        if (200 != $response->getStatusCode()) {
	            throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
	        }

	        $results = \json_decode($response->getBody(true), true);
	        $hasLoaded = true;
	    }

	    return $results;
	}

	/**
	 * Get Global City
	 * 
	 * @param unknown_type $id
	 * @return NULL|mixed
	 */
	public function getGlobalCityById($id)
	{
	    $response = $this->request->get($this->chromediaApiUri.'/cities/'.$id);
	    if (200 != $response->getStatusCode()) {
	        if (404 == $response->getStatusCode()){
	            return null;
	        }
	        else {
	            throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
	        }
	    }

	    return \json_decode($response->getBody(true), true);
	}

	/**
	 * Get Local City 
	 * 
	 * @param unknown_type $id
	 * @return \HealthCareAbroad\HelperBundle\Entity\City or NULL 
	 */
	public function getCityById($id)
	{
	    return $this->doctrine->getRepository('HelperBundle:City')->find($id);
	}

	/**
	 * Get Local City by slug
	 * 
	 * @param string $slug
	 * @return \HealthCareAbroad\HelperBundle\Entity\City or NULL
	 */
	public function getCityBySlug($slug)
	{
	    static $cityBySlugs = array();
	    if (!isset($cityBySlugs[$slug])) {
	        $cityBySlugs[$slug] = $this->doctrine->getRepository('HelperBundle:City')->findOneBySlug($slug);
	    }

	    return $cityBySlugs[$slug];
	}

	public function getActiveCitiesByCountry(Country $country)
	{
	    $criteria = array('status' => City::STATUS_ACTIVE, 'country' => $country);

	    return $this->doctrine->getEntityManager()->getRepository('HelperBundle:City')->findBy($criteria);
	}
	
	public function getActiveCitiesByCountryId($countryId)
	{
	    $country = $this->getCountryById($countryId);

	    return $this->getActiveCitiesByCountry($country);
	}
	
	public function getListActiveCitiesByCountry(Country $country)
	{
	    $cities = array();
	    $result = $this->getActiveCitiesByCountry($country);
	    foreach($result as $each)
	        $cities[] = array('id' => $each->getId(), 'name' => $each->getName());

	    return $cities;
	}
	
	public function getListActiveCitiesByCountryId($countryId)
	{
	    $cities = array();
	    $result = $this->getActiveCitiesByCountryId($countryId);
	    foreach($result as $each)
	        $cities[] = array('id' => $each->getId(), 'name' => $each->getName());

	    return $cities;
	}

	public function createCityFromArray(array $data)
	{
	    $requiredFields = array('id', 'name', 'country');

	    if(isset($data['geoCountry']) && isset($data['geoCountry']['id'])) {
	        $data['country'] = $this->getCountryById($data['geoCountry']['id']);
	    }

	    foreach ($requiredFields as $key) {
	        if (!\array_key_exists($key, $data)) {
	            throw LocationServiceException::missingRequiredCityDataKey($key);
	        }
	    }

	    $city = new City();
	    $city->setId($data['id']);
	    $city->setName($data['name']);
	    $city->setCountry($data['country']);
	    $city->setInstitutionId(isset($data['institutionId']) ? $data['institutionId'] : 0);
	    $city->setStatus(isset($data['status']) ? $data['status'] : City::STATUS_ACTIVE);
	    if (isset($data['slug'])){
	        $city->setSlug($data['slug']);
	    }

	    /* Disabled to avoid duplicate persist!
        if(isset($data['geoState']) && isset($data['geoState']['id'])) {
	        $state = $this->getStateById($data['geoState']['id']);
	    if(!$state) {
    	    $data['geoState']['country'] = $data['country'];
    	    $state = $this->createStateFromArray($data['geoState']);
	    }
	    $city->setState($state);
	    } */

	    return $city;
	}
	
}