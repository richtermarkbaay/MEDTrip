<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class CityListFilter extends ListFilter
{

	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
		$this->entityRepository = $doctrine->getEntityManager()->getRepository('HelperBundle:City');		
		
		// Add country in validCriteria
		$this->addValidCriteria('country');
	}

	function setFilterOptions()
	{
		$this->setCountryFilterOption();	

		$this->setStatusFilterOption();
	}

	function setCountryFilterOption()
	{		

		// Set The Filter Option 
		$countries = $this->doctrine->getEntityManager()->getRepository('HelperBundle:Country')->findByStatus(1);
		$options = array('all' => 'All');
		foreach($countries as $each) {
			$options[$each->getId()] = $each->getName();
		}

		$this->filterOptions['country'] = array(
			'label' => 'Country',
			'selected' => $this->queryParams['country'],
			'options' => $options
		);
	}
	
	function setFilteredResult()
	{
		if($this->queryParams['country'] != 'all') {
			$countryId = $this->queryParams['country'];
			$country = $this->doctrine->getEntityManager()->getRepository('HelperBundle:Country')->find($countryId);			
			$this->criteria['country'] = $country;
		}

		$this->filteredResult = $this->entityRepository->findBy($this->criteria);
	}
}