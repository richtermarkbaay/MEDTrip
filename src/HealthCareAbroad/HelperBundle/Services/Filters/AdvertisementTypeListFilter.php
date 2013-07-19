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
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('AdvertisementBundle:AdvertisementType', 'a');

    	if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
    		$this->queryBuilder->andWhere('a.status = :status');
    		$this->queryBuilder->setParameter('status', $this->queryParams['status']);
    	}
    	
    	$sort = "a.name " . $this->sortOrder;

    	$this->queryBuilder->add('orderBy', $sort);
    	
    	$this->pagerAdapter->setQueryBuilder($this->queryBuilder);

    	$this->filteredResult = $this->pager->getResults();
    	
    }
}