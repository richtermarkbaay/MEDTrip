<?php 
/**
 * 
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;


class AdvertisementTypeListFilter extends DoctrineOrmListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    }

    function setFilterOptions()
    {
        $this->setStatusFilterOption();
        
    }

    function setFilteredResults()
    {
        $queryBuilder = $this->pager->getAdapter()->getQueryBuilder();
        
        $queryBuilder->select('a')->from('AdvertisementBundle:AdvertisementType', 'a');

    	if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
    		$queryBuilder->andWhere('a.status = :status');
    		$queryBuilder->setParameter('status', $this->queryParams['status']);
    	}
    	
    	$sort = "a.name " . $this->sortOrder;

    	$queryBuilder->add('orderBy', $sort);
    	
    	$this->pagerAdapter->setQueryBuilder($this->queryBuilder);

    	$this->filteredResult = $this->pager->getResults();
    	
    }
}