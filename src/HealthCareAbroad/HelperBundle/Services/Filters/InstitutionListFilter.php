<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;


use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class InstitutionListFilter extends ListFilter
{

	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
		$this->entityRepository = $this->doctrine->getEntityManager()->getRepository('InstitutionBundle:Institution');

	}

	function setFilterOptions()
	{
		$statusOptions = array('all' => 'All') + InstitutionStatus::getStatusList();
		$this->setStatusFilterOption($statusOptions);
	}

	function setFilteredResult()
	{
		$this->filteredResult = $this->entityRepository->findBy($this->criteria);
	}
}