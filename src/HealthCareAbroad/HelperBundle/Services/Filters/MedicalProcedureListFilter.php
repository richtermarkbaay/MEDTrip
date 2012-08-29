<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\ORM\EntityManager;

class MedicalProcedureListFilter extends ListFilter
{

	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
		$this->entityRepository = $doctrine->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalProcedure');		
		
		// Add medicalProcedureType in validCriteria
		$this->addValidCriteria('medicalProcedureType');
	}

	function setFilterOptions()
	{
		$this->setMedicalProcedureTypeFilterOption();

		$this->setStatusFilterOption();
	}

	function setMedicalProcedureTypeFilterOption()
	{		
		// Set The Filter Option 
		$procedureTypes = $this->doctrine->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalProcedureType')->findByStatus(1);
		$options = array('all' => 'All');
		foreach($procedureTypes as $each) {
			$options[$each->getId()] = $each->getName();
		}

		$this->filterOptions['medicalProcedureType'] = array(
			'label' => 'Procedure Type',
			'selected' => $this->queryParams['medicalProcedureType'],
			'options' => $options
		);
	}

	function setFilteredResult()
	{
		$procedureTypeId = $this->queryParams['medicalProcedureType'];

		if($procedureTypeId != 'all') {
			$procedureType = $this->doctrine->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalProcedureType')->find($procedureTypeId);			
			$this->criteria['medicalProcedureType'] = $procedureType;
		}

		$this->filteredResult = $this->entityRepository->findBy($this->criteria);
	}
}