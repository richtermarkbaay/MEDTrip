<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class InstitutionMedicalCenterListFilter extends ListFilter
{

	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
		$this->entityRepository = $doctrine->getEntityManager()->getRepository('InstitutionBundle:InstitutionMedicalCenter');
	}

	function setFilterOptions()
	{
		$this->setStatusFilterOption();
	}

	function setFilteredResult()
	{
		$em = $this->doctrine->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($this->queryParams['institutionId']);

		$this->criteria['institution'] = $institution;
		$this->filteredResult = $this->entityRepository->findBy($this->criteria);
	}
}