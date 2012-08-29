<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class MedicalCenterListFilter extends ListFilter
{

	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
		$this->entityRepository = $doctrine->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalCenter');
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