<?php 
/**
 * 
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;


class AdvertisementTypeListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    }

    function setFilterOptions()
    {
        $this->setStatusFilterOption();
        
    }

    function buildQueryBuilder()
    {
        $this->queryBuilder->select('a')->from('AdvertisementBundle:AdvertisementType', 'a');

    	if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
    		$this->queryBuilder->andWhere('a.status = :status');
    		$this->queryBuilder->setParameter('status', $this->queryParams['status']);
    	}
    	
    	$sort = "a.name " . $this->sortOrder;

    	$this->queryBuilder->add('orderBy', $sort);
    	
    }
}