<?php

namespace HealthCareAbroad\SearchBundle\Classes;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;
abstract class SearchCategoryBuilder
{
	public $pager;
	protected $queryParams = array();
	protected $filteredResult = array();
	
	protected $pagerDefaultOptions = array('limit' => 10, 'page' => 1);
	
	function prepare($queryParams = array())
	{
		$this->setQueryParamsAndCriteria($queryParams);
		$this->setPager();
	}
	
	public function getResults($queryBuilder){
		
		$pageResult = $this->setPager($queryBuilder);
		
// 		$data = $pageResult->getResults();
		
		foreach ($pageResult->getResults() as $val){
			echo "<pre>";
			print_r($val);
			echo "</pre>";
			exit;
		}
	}
	
   function setPager()
    {
    	$adapter = new DoctrineOrmAdapter($this->queryBuilder);
    	
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