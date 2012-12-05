<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Symfony\Component\Routing\RouteCollection;

use Doctrine\ORM\Query\ResultSetMapping;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
abstract class SearchResultBuilder
{   
    const FILTER_KEY_ALL = 'all';
    const FILTER_LABEL_ALL = 'All';
    protected $criteria = array();
    protected $queryParams = array();
    protected $validCriteria = array('status');
    protected $defaultParams = array();
    protected $sortBy;
    protected $sortOrder = 'asc';
    protected $filterOptions = array();
    protected $filteredResult = array();
	protected $queryBuilder;

	/**
	 * @desc Default options value for Status Filter Option
	 * @var array
	 */
	protected $statusFilterOptions = array(1 => 'Active', 0 => 'Inactive', self::FILTER_KEY_ALL => self::FILTER_LABEL_ALL);
	protected $pagerDefaultOptions = array('limit' => 10, 'page' => 1);
	
	/**
	 * @var Symfony\Bundle\FrameworkBundle\Routing\Router
	 */
	protected $router;
	
	/**
	 * @desc Prepare the ListFilter object
	 * @param array $queryParams
	 */
	function setQueryParamsAndCriteria($queryParams = array())
	{	     
	    $this->queryParams = $queryParams;
	    
	    foreach($this->validCriteria as $key) {
	
	        if(isset($queryParams[$key])) {
	            if(!is_null($queryParams[$key]) && $queryParams[$key] != self::FILTER_KEY_ALL)
	                $this->criteria[$key] = $queryParams[$key];
	        }
	        else {
	         
	            // no param, check default first
	            $this->queryParams[$key] = \array_key_exists($key, $this->defaultParams) ? $this->defaultParams[$key] : self::FILTER_KEY_ALL;
	        }
	    }
	
	    if (isset($this->queryParams['sortBy'])) {
	        $this->sortBy = $this->queryParams['sortBy'];
	    }
	
	    if (isset($this->queryParams['sortOrder']))
	        $this->sortOrder = $this->queryParams['sortOrder'];
	}
	
	/**
	 * @desc Sets Status Filter Option
	 */
	function setStatusFilterOption($statusFilterOptions = array())
	{
	    if(count($statusFilterOptions))
	        $this->statusFilterOptions = $statusFilterOptions;
	
	    $this->filterOptions['status'] = array(
	                    'label' => 'Status',
	                    'selected' => $this->queryParams['status'],
	                    'options' => $this->statusFilterOptions
	    );
	}
	
	/**
	 * @desc Add a new valid criteria
	 * @param string $val
	 */
	function addValidCriteria($val)
	{
	    array_push($this->validCriteria, $val);
	}
	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
		$this->queryBuilder = $doctrine->getEntityManager()->createQueryBuilder();
	}
	public function setRouter(Router $router)
	{
	    $this->router = $router;
	}
    public function search(array $criteria, SearchAdminPagerService $p)
    {
        $queryParams = array('page' => $criteria['page']);
  
        $this->setQueryParamsAndCriteria($queryParams);
    
        $this->queryBuilder = $this->buildQueryBuilder($criteria);
 
        $pager = $p->searchPager($this->queryBuilder, $this->queryParams);
        $results = $pager->getResults();
        
        $arr = array();
        
        foreach ($results as $val) {
            $arr[] = $this->buildResult($val);
        }
        
        return $arr;
    }
    
    abstract protected function buildQueryBuilder($criteria);
    
    abstract protected function buildResult($val);
}