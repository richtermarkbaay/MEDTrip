<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;


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
		$this->statusFilterOptions = array(
			'all' => 'All',
			Institution::ACTIVE => 'Active',
			Institution::INACTIVE => 'Inactive',
			Institution::APPROVED => 'Approved',
			Institution::UNAPPROVED => 'Unapproved',
			Institution::SUSPENDED => 'Suspended'
		);

		$this->setStatusFilterOption();
	}

	function setFilteredResult()
	{
		$this->filteredResult = $this->entityRepository->findBy($this->criteria);
	}
}