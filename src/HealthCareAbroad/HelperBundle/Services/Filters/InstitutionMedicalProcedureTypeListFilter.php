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
		$this->setMedicalCenterOption();

		$this->setStatusFilterOption();
	}
	
	function setMedicalCenterOption()
	{
		$em = $this->doctrine->getEntityManager();
		$medicalCenters = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersList($this->queryParams['id']);

		$options['all'] = 'All';
		foreach($medicalCenters as $each) {
			$options[$each['id']] = $each['name'];
		}

		$this->filterOptions['medicalCenter'] = array(
			'label' => 'Medical Center',
			'selected' => $this->queryParams['medicalCenter'],
			'options' => $options
		);
	}

	function setFilteredResult()
	{	
		$em = $this->doctrine->getEntityManager();
		$institution = $em->getRepository('InstitutionBundle:Institution')->find($this->queryParams['id']);

		$medicalCenterId = $this->queryParams['medicalCenter'];
		$status = $this->queryParams['status'];

		if($medicalCenterId != 'all') {
			$status = $this->queryParams['status'] == 'all' ? null : $this->queryParams['status'];
			$this->filteredResult = $this->entityRepository->getByInstitutionIdAndMedicalCenterId($institution->getId(), $medicalCenterId, $status);
		} else {

			unset($this->criteria['medicalCenter']);
			$this->criteria['institution'] = $institution;
			
			$this->filteredResult = $this->entityRepository->findBy($this->criteria);
		}
	}
}