<?php

namespace HealthCareAbroad\HelperBundle\Services;

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
	
	/**
	 * @var string
	 */
	private $chromediaApiUri;
	
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
	
	public function getGlobalCountryList()
	{
	    static $hasLoaded = false;
	    
	    if (!$hasLoaded) {
	        
	        $response = $this->request->get($this->chromediaApiUri.'/countries');
	        if (200 != $response->getStatusCode()) {
	            throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
	        }
	        
	        $this->loadedGlobalCountryList = \json_decode($response->getBody(true), true);
	        $hasLoaded = true;
	    }
	
	    return $this->loadedGlobalCountryList;
	}
	
	public function getGlobalCitiesListByContry($countryId)
	{
	    $response = $this->request->get($this->chromediaApiUri.'/country/'.$countryId.'/cities');
	    if (200 != $response->getStatusCode()) {
	        throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
	    }
	    //var_dump(\json_decode($response->getBody(true), true)); exit;
	    return \json_decode($response->getBody(true), true);
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
	        if (!\array_key_exists($key, $data)) {
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
	
	public function getGlobalCityById($id)
	{
	    $response = $this->request->get($this->chromediaApiUri.'/city/'.$id);
	    if (200 != $response->getStatusCode()) {
	        throw LocationServiceException::failedApiRequest($response->getRequest()->getUrl(false), $response->getBody(true));
	    }
	    
	    return \json_decode($response->getBody(true), true);
	}
	
	public function createCityFromArray(array $data)
	{
	    $requiredFields = array('id', 'name', 'countryId', 'slug');
	    foreach ($requiredFields as $key) {
	        if (!\array_key_exists($key, $data)) {
	            throw LocationServiceException::missingRequiredCityDataKey($key);
	        }
	    }
	    
	    $city = new City();
	    $city->setId($data['id']);
	    $city->setName($data['name']);
	    $city->setSlug($data['slug']);
	    $country=$this->getCountryById($data['countryId']);
	    $city->setCountry($country);
	    $city->setStatus(City::STATUS_ACTIVE);
	    
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
	
	/**
	 * Hackish way to use this service without injecting it on other services.
	 * 
	 * @return \HealthCareAbroad\HelperBundle\Services\LocationService
	 */
	public static function getCurrentInstance()
	{
	    return self::$instance;
	}
}