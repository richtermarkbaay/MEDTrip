<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
use Doctrine\ORM\Query\ResultSetMapping;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;
abstract class SearchResultBuilder
{
	protected $queryParams = array();
	protected $queryBuilder;
	protected $pager;
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
	
    public function search(array $criteria)
    {
<<<<<<< HEAD
        var_dump($criteria);exit;

=======
    
>>>>>>> d6fb2b1a2099f3ef744c0f3f5895a2e3e7160b24
        $queryBuilder = $this->buildQueryBuilder($criteria);
        print_r($queryBuilder);
        exit;
//         $pager->setQueryBuilder($queryBuilder);
        
        $pager = $this->setPager($queryBuilder);
        
        $results = $pager->getResults();
        
      
        
        $arr = array();
        
        foreach ($results as $ea) {
            $arr[] = $this->buildResult($ea);
        }
        
        return $arr;
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
    
    abstract protected function buildQueryBuilder($criteria);
    
    abstract protected function buildResult();
    
    
}