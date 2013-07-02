<?php
/**
 * @autor Alnie Jacobe
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Doctrine\ORM\QueryBuilder;

class GlobalCountryListFilter extends ArrayListFilter
{
	function __construct($doctrine)
	{
		parent::__construct($doctrine);

		// set default status filter to active
		$this->defaultParams = array('status' => Country::STATUS_ACTIVE);
	
		// Add treatment in validCriteria
		$this->serviceDependencies = array('services.location');
	}
	
    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }

    function setFilteredResults()
    {
        $this->queryParams['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
        $dataFromAPi = $this->getInjectedDependcy('services.location')->getGlobalCountries($this->queryParams);
        
        $this->pager->getAdapter()->setData($dataFromAPi);
        
        $this->filteredResult = $this->pager->getResults();
    }
}