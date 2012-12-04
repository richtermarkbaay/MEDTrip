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
	protected $router;
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
	/**
	 * @var Symfony\Bundle\FrameworkBundle\Routing\Router
	 */
	private $router;
	
	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
		$this->queryBuilder = $doctrine->getEntityManager()->createQueryBuilder();
	}
	
    public function search(array $criteria)
    {
        $queryBuilder = $this->buildQueryBuilder($criteria);
  
        $pager = $this->setPager($queryBuilder);
        
        $results = $pager->getResults();
        
        $arr = array();
        
        foreach ($results as $val) {
            $arr[] = $this->buildResult($val);
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
    
    abstract protected function buildResult($val);
    
    
}