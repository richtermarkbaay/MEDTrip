<?php 
/**
 * @autor Chaztine
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class NewsListFilter extends ListFilter
{
	
	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
		$this->entityRepository = $doctrine->getEntityManager()->getRepository('HelperBundle:News');
	}

	function setFilterOptions()
	{
		$this->setStatusFilterOption();
	}

	function setFilteredResult()
	{
		$this->filteredResult = $this->entityRepository->findBy($this->criteria);
	}
}