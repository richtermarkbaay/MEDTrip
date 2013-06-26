<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Doctrine\ORM\QueryBuilder;

class CountryListFilter extends ListFilter
{
	function __construct($doctrine)
	{
		parent::__construct($doctrine);
	
		// set default status filter to active
		$this->defaultParams = array('status' => Country::STATUS_ACTIVE);
	
		// Add treatment in validCriteria
	}
	
    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }

    function buildQueryBuilder()
    {
        $this->queryBuilder->select('c')->from('HelperBundle:Country', 'c');    

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('c.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        if($this->sortBy == 'abbr') {
        		$sortBy = $this->sortBy ? $this->sortBy : 'abbr';
        	$sort = "c.$sortBy " . $this->sortOrder;
        } else {
        	$sortBy = $this->sortBy ? $this->sortBy : 'name';
        	$sort = "c.$sortBy " . $this->sortOrder;
        }         
        $this->queryBuilder->add('orderBy', $sort);
    }
}