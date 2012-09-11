<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class MedicalProcedureTypeListFilter extends ListFilter
{

	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
		$this->entityRepository = $doctrine->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalProcedureType');		
		
		// Add medicalCenter in validCriteria
		$this->addValidCriteria('medicalCenter');
	}

	function setFilterOptions()
	{
		$this->setMedicalCenterFilterOption();	

		$this->setStatusFilterOption();
	}

	function setMedicalCenterFilterOption()
	{		

		// Set The Filter Option 
		$medicalCenters = $this->doctrine->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalCenter')->findByStatus(1);
		$options = array(ListFilter::FILTER_KEY_ALL => 'All');
		foreach($medicalCenters as $each) {
			$options[$each->getId()] = $each->getName();
		}

		$this->filterOptions['medicalCenter'] = array(
			'label' => 'Medical Center',
			'selected' => $this->queryParams['medicalCenter'],
			'options' => $options
		);
	}
	
	function setFilteredResult()
	{
		$medicalCenterId = $this->queryParams['medicalCenter'];

		if($medicalCenterId != ListFilter::FILTER_KEY_ALL) {
			$medicalCenter = $this->doctrine->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalCenter')->find($medicalCenterId);			
			$this->criteria['medicalCenter'] = $medicalCenter;
		}

		$this->filteredResult = $this->entityRepository->findBy($this->criteria);
	}
}