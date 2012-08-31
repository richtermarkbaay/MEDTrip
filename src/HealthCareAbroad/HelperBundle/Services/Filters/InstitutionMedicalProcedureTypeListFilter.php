<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class InstitutionMedicalProcedureTypeListFilter extends ListFilter
{
	
	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
		$this->entityRepository = $doctrine->getEntityManager()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType');
		
		$this->addValidCriteria('medicalCenter');
	}

	function setFilterOptions()
	{
		$this->setStatusFilterOption();
	}

	function setFilteredResult()
	{
		$institutionMedicalCenter = $this->doctrine->getEntityManager()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($this->queryParams['imcId']);
		$this->criteria['institutionMedicalCenter'] = $institutionMedicalCenter;
		$this->filteredResult = $this->entityRepository->findBy($this->criteria);
	}
}