<?php
/*
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\SearchBundle\Services\Admin;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

class SearchAdminPagerService
{
    protected $pager;
	protected $pagerDefaultOptions = array('limit' => 10, 'page' => 1);
	
	public function searchPager($queryBuilder, $queryParams){
	    $this->queryBuilder = $queryBuilder;
	    $this->queryParams = $queryParams;
	
	    $pager = $this->setPager($this->queryBuilder);
	
	    $result = $this->pager;
	
	    return $result;
	}
    function setPager()
    {
    	$adapter = new DoctrineOrmAdapter($this->queryBuilder );
    	$params['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
    	$params['limit'] = isset($this->queryParams['limit']) ? $this->queryParams['limit'] : $this->pagerDefaultOptions['limit'];
    
    	$this->pager = new Pager($adapter, $params);
    }
    
    function getPager()
    {
    	return $this->pager;
    }

}
