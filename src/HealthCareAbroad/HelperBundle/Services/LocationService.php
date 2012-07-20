<?php

namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\HelperBundle\Entity\Country;

class LocationService
{
	protected $doctrine;

	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
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
		$result = $this->getActiveCitiesByCountryId($countryId);
		foreach($result as $each)
			$cities[] = array('id' => $each->getId(), 'name' => $each->getName());
		
		return $cities;
	}
	
	public function getActiveCountries()
	{
		return $this->doctrine->getEntityManager()->createQueryBuilder()
			->add('select', 'c')
			->add('from', 'HelperBundle:Country c')
			->add('where', 'c.status = 1');
	}
}