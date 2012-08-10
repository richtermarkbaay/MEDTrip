<?php
namespace HealthCareAbroad\SearchBundle\Services;

use HealthCareAbroad\SearchBundle\Constants;
use Doctrine\ORM\EntityManager;

class SearchService
{
	private $entityManager;
	private $repositoryMap;
	
	public function __construct(EntityManager $entityManager) 
	{
		$this->entityManager = $entityManager;

		$this->repositoryMap = array(
			Constants::SEARCH_CATEGORY_INSTITUTION => 'InstitutionBundle:Institution',
			Constants::SEARCH_CATEGORY_CENTER => 'MedicalProcedureBundle:MedicalCenter',
			Constants::SEARCH_CATEGORY_PROCEDURE_TYPE => 'MedicalProcedureBundle:MedicalProcedureType',
			Constants::SEARCH_CATEGORY_PROCEDURE => 'MedicalProcedureBundle:MedicalProcedure'
		);		
	}

	public function initiate($searchCriteria = array())
	{
		$repository = $this->entityManager->getRepository($this->repositoryMap[$searchCriteria['category']]);
		
		return $repository->search($searchCriteria['term']); 
	}

}