<?php
/**
 * @autor Alnie Jacobe
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Doctrine\ORM\QueryBuilder;

class GlobalCountryListFilter extends ListFilter
{
	function __construct($doctrine)
	{
		parent::__construct($doctrine);
		
		// TODO: Temparary fix for pager array adapter type.
		$this->dataType = 'array';
	
		// set default status filter to active
		$this->defaultParams = array('status' => Country::STATUS_ACTIVE);
	
		// Add treatment in validCriteria
		$this->serviceDependencies = array('services.location');
	}
	
    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }

    function buildQueryBuilder()
    {   
        $countryList = $this->getInjectedDependcy('services.location')->getGlobalCountryListByStatus($this->queryParams['status']);
        // TODO: Temparary fix for pager array adapter type.
        $this->queryBuilder = $countryList;        
    }
}