<?php

namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\HelperBundle\Entity\State;

use Doctrine\ORM\Query;

use HealthCareAbroad\HelperBundle\Entity\City;

use HealthCareAbroad\HelperBundle\Exception\LocationServiceException;

use HealthCareAbroad\HelperBundle\Entity\Country;

class LocationService
{
	protected $doctrine;

	/**
	 * @var ChromediaGlobalRequest
	 */
	private $request;
	
	private $loadedGlobalCountryList = array();
	
	private $loadedGlobalCityList = array();
	
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
	
	public function getGlobalCountryById($id)
	{
	    if (!\array_key_exists($id, $this->loadedGlobalCountryList)) {
	        $response = $this->request->get($this->chromediaApiUri.'/country/'.$id);
	        if (200 != $response->getStatusCode()) {
	            throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
	        }
	        $this->loadedGlobalCountryList[$id] = \json_decode($response->getBody(true), true);
        }
        
        return $this->loadedGlobalCountryList[$id];
	}
	
	
	
	public function saveGlobalCountry($country)
	{
	    if(!is_array($country)) {
	        $country = self::countryObjectToArray($country);
	    }

	    $response = $this->request->post($this->chromediaApiUri.'/country/add', array('data' => $country));

	    return \json_decode($response->getBody(true), true);
	}

	static function countryObjectToArray(Country $country)
	{
	    return array(
            'id' => $country->getId(),
            'name' => $country->getName(),
            'abbr' => $country->getAbbr(),
            'code' =>  $country->getCode(),
            'slug' => $country->getSlug(),
            'status' => $country->getStatus(),
	    );
	}
	
	public function updateStatusGlobalCountry($id)
	{
	    $response = $this->request->get($this->chromediaApiUri.'/country/'.$id.'/update-status');
	    
	    return $this->createCountryFromArray(\json_decode($response->getBody(true), true));
	}
	
	/**
	 * @deprecated
	 * @param array $params
	 */
	public function getGlobalCountries(array $params=array())
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

	public function getGlobalCityList()
	{
	    static $hasLoaded = false;
	     
	    if (!$hasLoaded) {
	         
	        $response = $this->request->get($this->chromediaApiUri.'/cities');
	        if (200 != $response->getStatusCode()) {
	            throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
	        }
	         
	        $this->loadedGlobalCityList = \json_decode($response->getBody(true), true);
	        $hasLoaded = true;
	    }

	    return $this->loadedGlobalCityList;
	}
	
	public function getGlobalCitiesListByContry($countryId)
	{
// 	    $response = $this->request->get($this->chromediaApiUri."/country/$countryId/cities");
// 	    if (200 != $response->getStatusCode()) {
// 	        throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
// 	    }
// 	    $citiesData = \json_decode($response->getBody(true), true);

	    $citiesDoc = \json_decode($this->couchDbService->get("country_$countryId"), true);
	    $citiesData = $citiesDoc['data'];

	    return $citiesData;
	}
	
	/**
	 * 
	 * @param Country $country
	 */
	public function saveCountry(Country $country)
	{
	   $em = $this->doctrine->getEntityManager();
	   $em->persist($country);
	   $em->flush(); 
	}
	
	public function getCountryById($id)
	{
	    $country = $this->doctrine->getRepository('HelperBundle:Country')->find($id);
	    
	    return $country;
	}
	
	public function getCountryBySlug($slug)
	{
	    static $countryBySlugs = array();
	    if (!isset($countryBySlugs[$slug])) {
	        $countryBySlugs[$slug] = $this->doctrine->getRepository('HelperBundle:Country')->findOneBySlug($slug);
	    }
	    
	    return $countryBySlugs[$slug];
	}
	
	public function getCityById($id)
	{
	    return $this->doctrine->getRepository('HelperBundle:City')->find($id);
	}
	
	public function getCityBySlug($slug)
	{
	    static $cityBySlugs = array();
	    if (!isset($cityBySlugs[$slug])) {
	        $cityBySlugs[$slug] = $this->doctrine->getRepository('HelperBundle:City')->findOneBySlug($slug);
	    }
	     
	    return $cityBySlugs[$slug];
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
	    $country->setAbbr(isset($data['abbr']) ? $data['abbr'] : '');
	    $country->setCode(isset($data['code']) ? $data['code'] : '');
	    $country->setStatus(Country::STATUS_ACTIVE);
	     
	    return $country;
	}
	
	/**
	 * Create Country instance from array $data
	 *
	 * @param array $data
	 * @return \HealthCareAbroad\HelperBundle\Entity\State
	 */
	public function createStateFromArray(array $data)
	{
	    $requiredFields = array('id', 'name', 'country');
	    
	    if(isset($data['geoCountry']) && isset($data['geoCountry']['id'])) {
	        $globalStateData['country'] = $this->getCountryById($data['geoCountry']['id']);	        
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
	    $state->setAdministrativeCode(isset($data['administrativeCode']) ? $data['administrativeCode'] : null);
	    $state->setStatus(isset($data['status']) ? $data['status'] : State::STATUS_ACTIVE);

	    return $state;
	}

	public function createCityFromArray(array $data)
	{
	    $requiredFields = array('id', 'name');
	    foreach ($requiredFields as $key) {
	        if (!\array_key_exists($key, $data)) {
	            throw LocationServiceException::missingRequiredCityDataKey($key);
	        }
	    }

	    $city = new City();
	    $city->setId($data['id']);
	    $city->setName($data['name']);
	    if (isset($data['slug'])){
	        $city->setSlug($data['slug']);
	    }
	    $city->setStatus(City::STATUS_ACTIVE);

	    if(isset($data['geoCountry'])) {
	        $countryData = isset($data['geoCountry']) ? $data['geoCountry'] : $data['geo_country'];
	        $country = $this->createCountryFromArray($countryData);
	        $city->setCountry($country);
	    }

	    if(isset($data['geoCountry'])) {
	        $countryData = isset($data['geoCountry']) ? $data['geoCountry'] : $data['geo_country'];
	        $country = $this->createCountryFromArray($countryData);
	        $city->setCountry($country);
	    }

	    return $city;
	}

	public function getActiveCitiesByCountry(Country $country)
	{
		$criteria = array('status'=>1, 'country'=>$country);
		return $this->doctrine->getEntityManager()->getRepository('HelperBundle:City')->findBy($criteria);
	}

	public function getActiveCitiesByCountryId($countryId)
	{
		$country = $this->doctrine->getEntityManager()->getRepository('HelperBundle:Country')->find($countryId);
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
	
	public function getQueryBuilderForCountries()
	{
		return $this->doctrine->getEntityManager()->getRepository('HelperBundle:Country')->getQueryBuilderForCountries();
	}

	public function getActiveCountriesWithCities()
	{
	    $qb = $this->getQueryBuilderForCountries();
	    $qb->addSelect('b');
	    $qb->leftJoin('c.cities', 'b');

        return $qb->getQuery()->getResult();
	}

	public function getActiveCountries($hydrationMode=Query::HYDRATE_ARRAY)
	{
	    if(!static::$activeCountries) {
	        $qb = $this->getQueryBuilderForCountries();
	        $qb->andWhere('c.status = 1')->orderBy('c.name');
	        static::$activeCountries = $qb->getQuery()->getResult($hydrationMode);	        
	    }

	    return static::$activeCountries;
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

	public function findGlobalCityById($id)
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

	public function addGlobalCity($cityData)
	{
	    $response = $this->request->post($this->chromediaApiUri.'/cities', array('geoCity' => $cityData));
	    //echo $response->getBody(true);
	    $city = \json_decode($response->getBody(true), true);

	    return $city;
	}

	public function updateGlobalCity($cityData, $id)
	{
	    $response = $this->request->post($this->chromediaApiUri."/cities/$id", array('geoCity' => $cityData));

	    $city = \json_decode($response->getBody(true), true);

	    return $city;
	}

	public function updateGlobalCityStatus($id, $status)
	{
	    $response = $this->request->post($this->chromediaApiUri. "/cities/$id/update-status", array('status' => $status));

	    if (200 != $response->getStatusCode()) {
	        throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getReasonPhrase());
	    }

	    return \json_decode($response->getBody(true), true);
	}

	//-------------------------------------
	// Start state related functions
	public function addGlobalGeoState($stateData)
	{
	    $response = $this->request->post($this->chromediaApiUri.'/states', array('geoState' => $stateData));
        //echo $response->getBody(true);
	    if (201 != $response->getStatusCode()) {
	        throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getReasonPhrase());
	    }

	    return \json_decode($response->getBody(true), true);
	}
	
	
	// TODO: This is currently not being used! DEPRECATED??
	public function findGlobalStatesByCountry($countryId, $institutionId = null)
	{
	    $url = $this->chromediaApiUri.'/states&country_id='.$countryId;
	    
	    if($institutionId) {
	        $url .= "&institution_id=$institutionId";
	    }
	    
	    $response = $this->request->get($url);
	    if (200 != $response->getStatusCode()) {
	        throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getReasonPhrase());
	    }
	    
	    return \json_decode($response->getBody(true), true);
	}
	
	public function findStateById($stateId)
	{
	    return $this->doctrine->getRepository('HelperBundle:State')->find($stateId);
	}
	
	public function getGlobalStateById($stateId)
	{
	    $response = $this->request->get($this->chromediaApiUri.'/states/'.$stateId);
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
	
	public function updateCity($data, $id)
	{
	    if($cityObj = $this->doctrine->getRepository('HelperBundle:City')->find($id)) {
	        $em = $this->doctrine->getEntityManagerForClass('HelperBundle:City');

	        $country = $this->doctrine->getRepository('HelperBundle:Country')->find($data['geoCountry']['id']);

            if(!$country) {
                $country = $this->createCountryFromArray($data['geoCountry']);
                $em->persist($country);
            }

	        if(!isset($data['geoState']) || !$data['geoState']) {
	            $cityObj->setState(null);
	        } else {
	            $state = $this->doctrine->getRepository('HelperBundle:State')->find($data['geoState']['id']);
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
	    }
	}

	/**
	 * This will add a new data in global cities (geo_cities) first
	 * and will save/update local city if success.
	 * @param array $data
	 * @return \HealthCareAbroad\HelperBundle\Entity\City
	 */
	public function addNewCity(array $data) {
	    // Save to global city.
	    $cityData = $this->addGlobalCity($data);
	
	    // Update local city if successfully saved in global city.
	    $city = $this->doctrine->getRepository('HelperBundle:City')->find($cityData['id']);
	    if(!$city) {
	        $city = new City();
	        $city->setId($cityData['id']);

	        $country = $this->getCountryById($data['geoCountry']);
	        $city->setCountry($country);
	    }
	    
	    if($data['geoState']) {
            $state = $this->doctrine->getRepository('HelperBundle:State')->find($data['geoState']);
            $city->setState($state ? $state : null);
	    }

	    $city->setName($cityData['name']);
	    $city->setStatus($cityData['status']);
	    $city->setSlug($cityData['slug']);
	
	    $em = $this->doctrine->getEntityManagerForClass('HelperBundle:City');
	    $em->persist($city);
	    $em->flush($city);
	
	    return $city;
	}
	
	/**
	 * This will add a new data in global states (geo_states) first
	 * and will save/update local states if success.
	 * @param array $data
	 * @return \HealthCareAbroad\HelperBundle\Entity\State
	 */
	public function addNewState(array $data) {
	    // Save to global state.
	    $stateData = $this->addGlobalGeoState($data);
	
	    // Update local state if successfully saved in global state.
	    $state = $this->doctrine->getRepository('HelperBundle:State')->find($stateData['id']);
	    if(!$state) {
	        $state = new State();
	        $state->setId($stateData['id']);

	        $country = $this->getCountryById($data['geoCountry']);
	        $state->setCountry($country);
	    }
	
	    $state->setName($stateData['name']);
	    $state->setStatus($stateData['status']);
	
	    $em = $this->doctrine->getEntityManagerForClass('HelperBundle:State');
	    $em->persist($state);
	    $em->flush($state);
	
	    return $state;
	}	
}