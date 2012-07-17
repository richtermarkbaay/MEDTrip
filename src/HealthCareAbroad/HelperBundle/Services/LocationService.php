<?php

namespace HealthCareAbroad\HelperBundle\Services;

class LocationService
{
	protected $doctrine;

	public function setDoctrine(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	public function getActiveCities()
	{
		return $this->doctrine->getEntityManager()->createQueryBuilder()
				->add('select', 'c')
				->add('from', 'HelperBundle:City c')
				->add('where', 'c.status = 1');
	}

	public function getActiveCountries()
	{
		return $this->doctrine->getEntityManager()->createQueryBuilder()
				->add('select', 'c')
				->add('from', 'HelperBundle:Country c')
				->add('where', 'c.status = 1');
	}
}