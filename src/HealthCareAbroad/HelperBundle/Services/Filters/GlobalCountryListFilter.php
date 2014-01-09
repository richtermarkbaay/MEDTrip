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
        
        $statuses = array(
            Country::STATUS_NEW => 'New',
            Country::STATUS_ACTIVE => 'Active',
            Country::STATUS_INACTIVE => 'Inactive',
            self::FILTER_KEY_ALL => self::FILTER_LABEL_ALL
        );
        $this->setStatusFilterOption($statuses);
    }

    function setNameFilterOption()
    {
        $this->filterOptions['name'] = array('label' => 'Country Name', 'value' => $this->queryParams['name']);
    }

    function setFilteredResults()
    {
        $golbalCountriesParams['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
        $golbalCountriesParams['limit'] = $this->pagerDefaultOptions['limit'];

        if($this->queryParams['name']) {
            $golbalCountriesParams['name'] = $this->queryParams['name'];
        }

        if($this->queryParams['status'] == 'all') {
            $golbalCountriesParams['active_only'] = 0;
        } else {
            $golbalCountriesParams['status'] = $this->queryParams['status'];
        }

        $dataFromAPi = $this->getInjectedDependcy('services.location')->getGlobalCountries($golbalCountriesParams);
        $this->pager->getAdapter()->setData($dataFromAPi);
        
        $this->filteredResult = $this->pager->getResults();
    }
}