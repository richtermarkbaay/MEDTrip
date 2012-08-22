<?php

namespace HealthCareAbroad\HelperBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CountryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CountryRepository extends EntityRepository
{
	function getCountryList() {
		$countries = $this->_em->getRepository('HelperBundle:Country')->findByStatus(1);
		$arrCountries = array();
		foreach($countries as $each){
			$arrCountries[$each->getId()] = $each->getName();
		}

		return $arrCountries;
	}
	
	public function getQueryBuilderForCountries()
	{
		return $this->_em->createQueryBuilder()
		->add('select', 'c')
		->add('from', 'HelperBundle:Country c')
		->add('where', 'c.status = 1');
	}
}