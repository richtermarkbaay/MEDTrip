<?php
namespace HealthCareAbroad\SearchBundle\Services;

use HealthCareAbroad\SearchBundle\Services\Admin\SearchResultBuilderFactory;

use HealthCareAbroad\SearchBundle\Classes\SearchCategoryBuilder;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;
use HealthCareAbroad\SearchBundle\Constants;
use HealthCareAbroad\AdminBundle\Entity\SearchAdminResults;
use HealthCareAbroad\HelperBundle\Entity\Country;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

/**
 * Temporary holder of all search related functionality
 *
 */
class AdminSearchService
{
	protected $queryBuilder;
	protected $queryParams = array();
	protected $category = array(
					Constants::SEARCH_CATEGORY_INSTITUTION => 'InstitutionBundle:Institution',
					Constants::SEARCH_CATEGORY_CENTER => 'InstitutionBundle:InstitutionMedicalCenter',
					Constants::SEARCH_CATEGORY_PROCEDURE_TYPE => 'TreatmentBundle:Treatment',
					Constants::SEARCH_CATEGORY_DOCTOR => 'DoctorBundle:Doctor',
					Constants::SEARCH_CATEGORY_SPECIALIZATION => 'TreatmentBundle:Specialization',
					Constants::SEARCH_CATEGORY_SUB_SPECIALIZATION => 'TreatmentBundle:SubSpecialization');
	
	/**
	 * @var SearchResultBuilderFactory
	 */
	private $factory;
	
	public function setSearchBuilderFactory(SearchResultBuilderFactory $sf)
	{
		$this->factory = $sf;
	}

	public function search(array $searchCriteria = array())
	{
		
		$builder = $this->factory->getBuilderByCategory($searchCriteria);
		
		$builder->search($searchCriteria);
		exit;
		
        //$this->hydrateSearchData($searchCriteria['category'], $results);
		
    	return $results;
	}
// 	public function hydrateSearchData($searchCategory, $searchResult)
// 	{
// 	    $searchData = new SearchAdminResults();
// 	    foreach ($searchResult as $data => $each)
// 	    {
// 	        $searchData->setDescription($each->getDescription());
// 	        if($searchCategory == Constants::SEARCH_CATEGORY_DOCTOR) {
// 	            $searchData->setFirstName($each->getFirstName());
// 	            $searchData->setMiddleName($each->getMiddleName());
// 	            $searchData->setLastName($each->getLastName());
// 	            $searchData->setName($searchData->getFullName());
// 	        }
// 	        else {
// 	            $searchData->setName($each->getName());
// 	        }
// 	        $searchData->setId($each->getId());
	        
// 	    }var_dump($searchData);exit;
// 	}
//     public function buildQueryBuilder($searchCriteria,$searchTerm)
//     {
//         $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
//         $this->queryBuilder->select('a')->from($this->category[$searchCriteria], 'a');

//     	if ($searchCriteria == Constants::SEARCH_CATEGORY_DOCTOR) {
//     		$this->queryBuilder->andWhere('a.firstName LIKE :seachTerm OR a.middleName LIKE :seachTerm OR a.lastName LIKE :seachTerm');
//     		$this->queryBuilder->setParameter('seachTerm', '%'.$searchTerm.'%');
//     	}
//     	else if ($searchCriteria == Constants::SEARCH_CATEGORY_CENTER) {
    		
//     		$this->queryBuilder->join('a.institution', 'b');
//     		$this->queryBuilder->join('a.institutionSpecializations', 'c');
//     		$this->queryBuilder->innerJoin('c.specialization', 'd');
//     		$this->queryBuilder->where('a.institution = b.id');
//     		$this->queryBuilder->andWhere('a.id = c.institutionMedicalCenter');
//     		$this->queryBuilder->andWhere('c.specialization = d.id');
//     		$this->queryBuilder->andWhere('a.name LIKE :name');
//     		$this->queryBuilder->setParameter('name', '%'.$searchTerm.'%');
    	
//     	}else{
//     		$this->queryBuilder->andWhere('a.name LIKE :name');
//     		$this->queryBuilder->setParameter('name', '%'.$searchTerm.'%');
//     	}
    	
//     	return $this->queryBuilder;
//     }
}