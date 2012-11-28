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
	
//     private $repositoryMap = array(
    				
//         Constants::SEARCH_CATEGORY_INSTITUTION => 'InstitutionBundle:Institution',
//         Constants::SEARCH_CATEGORY_CENTER => 'InstitutionBundle:InstitutionMedicalCenter',
//         Constants::SEARCH_CATEGORY_PROCEDURE_TYPE => 'MedicalProcedureBundle:Treatment',
//         Constants::SEARCH_CATEGORY_PROCEDURE => 'MedicalProcedureBundle:TreatmentProcedure'
//     );

    /**
     *
     * @param array $searchCriteria
     * @todo rename method
     */
//     public function initiate(array $searchCriteria = array())
//     {
//         $repository = $this->entityManager->getRepository($this->repositoryMap[$searchCriteria['category']]);

//         return $repository->search($searchCriteria['term']);
//     }
    
    function buildQueryBuilder(array $searchCriteria = array())
    {
    	$this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
    
    	if ($searchCriteria['category'] == 1) {
    		$this->queryBuilder->select('a')->from('InstitutionBundle:Institution', 'a');
    		$this->queryBuilder->andWhere('a.name = :name');
    		$this->queryBuilder->setParameter('name', $searchCriteria['term']);
    	}

    	$adapter = new DoctrineOrmAdapter($this->queryBuilder);
    	
    	$params['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
    	$params['limit'] = isset($this->queryParams['limit']) ? $this->queryParams['limit'] : $this->pagerDefaultOptions['limit'];
    	
    	$this->pager = new Pager($adapter, $params);
    	
		return $this->pager->getResults();
    }
    
    function getPager()
    {
    	return $this->pager;
    }
}