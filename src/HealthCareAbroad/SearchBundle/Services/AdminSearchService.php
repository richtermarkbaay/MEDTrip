<?php
namespace HealthCareAbroad\SearchBundle\Services;

use Doctrine\ORM\QueryBuilder;

use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;
use HealthCareAbroad\SearchBundle\Constants;
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
	protected $doctrine;
	protected $queryBuilder;
	protected $queryParams = array();
	public $pager;
	protected $pagerDefaultOptions = array('limit' => 10, 'page' => 1);
	protected $category = array('1' => 'InstitutionBundle:Institution', 
	                            '2' => 'InstitutionBundle:InstitutionMedicalCenter',
	                            '3' => 'TreatmentBundle:Treatment',
	                            '4' => 'TreatmentBundle:Treatment',
	                            '5' => 'DoctorBundle:Doctor',
            	                '6' => 'TreatmentBundle:Specialization',
	                            '7' => 'TreatmentBundle:SubSpecialization');
	
	/**
	 * @desc Prepare the ListFilter object
	 * @param array $queryParams
	 */
	function prepare($queryParams = array())
	{
		$this->setQueryParamsAndCriteria($queryParams);
		$this->buildQueryBuilder();
		$this->setPager();
	}
	
	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
		$this->queryBuilder = $doctrine->getEntityManager()->createQueryBuilder();
	}
    
    function buildQueryBuilder(array $searchCriteria = array())
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
    	if ($searchCriteria['category'] == Constants::SEARCH_CATEGORY_DOCTOR) {
    		$this->queryBuilder->select('a')->from($this->category[$searchCriteria['category']], 'a');
    		$this->queryBuilder->andWhere('a.firstName LIKE :name OR a.middleName LIKE :name OR a.lastName LIKE :name');
    		$this->queryBuilder->setParameter('name', '%'.$searchCriteria['term'].'%');
    	}
    	else {
    		$this->queryBuilder->select('a')->from($this->category[$searchCriteria['category']], 'a');
    		$this->queryBuilder->andWhere('a.name LIKE :name');
    		$this->queryBuilder->setParameter('name', '%'.$searchCriteria['term'].'%');
    	}
    	
    	$result = $this->setPager($this->queryBuilder);
    	
		return $result->getResults();
    }
    
   function setPager($query)
    {
    	$adapter = new DoctrineOrmAdapter($query);
    
    	$params['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
    	$params['limit'] = isset($this->queryParams['limit']) ? $this->queryParams['limit'] : $this->pagerDefaultOptions['limit'];
    
    	$this->pager = new Pager($adapter, $params);
    
    	return $this->pager;
    }
    
    function getPager()
    {
    	return $this->pager;
    }
}