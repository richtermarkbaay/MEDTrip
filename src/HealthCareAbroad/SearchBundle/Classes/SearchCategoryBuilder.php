<?php

namespace HealthCareAbroad\SearchBundle\Classes;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use HealthCareAbroad\AdminBundle\Entity\SearchAdminResults;

abstract class SearchCategoryBuilder
{
	protected $doctrine;
	protected $queryBuilder;
	public $pager;
	protected $queryParams = array();
	protected $filteredResult = array();
	
	protected $pagerDefaultOptions = array('limit' => 10, 'page' => 1);
	
	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
		$this->queryBuilder = $doctrine->getEntityManager()->createQueryBuilder();
	}
	
	function prepare($queryParams = array())
	{
		$this->setQueryParamsAndCriteria($queryParams);
		$this->setPager();
	}
	
	public function getResultForDoctors($queryBuilder){

	}
	
	//not yet done
	public function getResultForMedicalCenter($queryBuilder){
		$result = array();
		
		$pageResult = $this->setPager($queryBuilder);
		$array = $pageResult->getResults();
		
		foreach ($array as $val => $f){
			
			$institutionName = $f->getInstitution()->getName();
	
			$array = $f->getDescription() .",". $institutionName;
			
		}
	}
	
	public function getResults($queryBuilder){
	
		$pageResult = $this->setPager($queryBuilder);

		return $pageResult->getResults();
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