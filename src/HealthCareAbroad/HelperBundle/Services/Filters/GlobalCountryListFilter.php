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
        
		$this->addValidCriteria('name');

		// set default filters
		$this->defaultParams = array('status' => Country::STATUS_ACTIVE, 'name' => '');
	
		// Add treatment in validCriteria
		$this->serviceDependencies = array('services.location');
	}
	
    function setFilterOptions()
    {
        $this->setNameFilterOption();
        $this->setStatusFilterOption();
    }

    function setNameFilterOption()
    {
        $this->filterOptions['name'] = array('label' => 'State Name', 'value' => $this->queryParams['name']);
    }

    function setFilteredResults()
    {
        $golbalCountriesParams['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
        $golbalCountriesParams['limit'] = $this->pagerDefaultOptions['limit'];

        if($this->queryParams['name']) {
            $golbalCountriesParams['name'] = $this->queryParams['name'];
        }

        $dataFromAPi = $this->getInjectedDependcy('services.location')->getGlobalCountries($golbalCountriesParams);
        $this->pager->getAdapter()->setData($dataFromAPi);
        
        $this->filteredResult = $this->pager->getResults();
    }
}