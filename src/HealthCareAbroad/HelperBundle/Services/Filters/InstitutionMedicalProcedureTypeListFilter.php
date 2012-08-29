<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class InstitutionMedicalProcedureTypeListFilter extends ListFilter
{

	private $institution;
	
	function __construct($doctrine)
	{
		$this->doctrine = $doctrine;
		$this->entityRepository = $doctrine->getEntityManager()->getRepository('InstitutionBundle:InstitutionMedicalProcedureType');
		
		$this->addValidCriteria('medicalCenter');
	}

	function setFilterOptions()
	{
		$em = $this->doctrine->getEntityManager();
		$this->institution = $em->getRepository('InstitutionBundle:Institution')->find($this->queryParams['id']);

		$this->setMedicalCenterOption();

		$this->setStatusFilterOption();
	}
	
	function setMedicalCenterOption()
	{
		$institutionCenters = $this->institution->getInstitutionMedicalCenters();

		$options = array('all' => 'All');
		foreach($institutionCenters as $each) {
			$center = $each->getMedicalCenter();
			$options[$center->getId()] = $center->getName();
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
		$status = $this->queryParams['status'];

		if($medicalCenterId != 'all') {
			$status = $this->queryParams['status'] == 'all' ? null : $this->queryParams['status'];

			$this->filteredResult = $this->entityRepository->getByInstitutionIdAndMedicalCenterId($this->institution->getId(), $medicalCenterId, $status);
		} else {
			unset($this->criteria['medicalCenter']);
			$this->criteria['institution'] = $this->institution;			

			$this->filteredResult = $this->entityRepository->findBy($this->criteria);
		}
	}
}