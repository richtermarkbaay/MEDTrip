<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
use Doctrine\ORM\QueryBuilder;
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
	
		$this->setPager();
	}

    public function search(array $criteria)
    {
        var_dump($criteria);exit;

        $queryBuilder = $this->buildQueryBuilder($criteria);
//         print_r($queryBuilder);
//         exit;
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