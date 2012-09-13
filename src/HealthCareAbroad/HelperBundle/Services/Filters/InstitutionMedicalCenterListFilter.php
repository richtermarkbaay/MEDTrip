<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

class InstitutionMedicalCenterListFilter extends ListFilter
{
	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
		$this->entityRepository = $doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter');
	}

	function setFilterOptions()
	{
		$statusFilterOptions = array('all' => 'All') + InstitutionMedicalCenterStatus::getStatusList();
		$this->setStatusFilterOption($statusFilterOptions);
	}

	function setFilteredResult()
	{
		$institution = $this->doctrine->getRepository('InstitutionBundle:Institution')->find($this->queryParams['institutionId']);

		$this->criteria['institution'] = $institution;
		$this->filteredResult = $this->entityRepository->findBy($this->criteria);
	}
}