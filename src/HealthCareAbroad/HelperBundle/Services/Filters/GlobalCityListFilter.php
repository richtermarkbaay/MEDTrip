<?php
/**
 * @autor Alnie Jacobe
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Entity\City;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Doctrine\ORM\QueryBuilder;

class GlobalCityListFilter extends ArrayListFilter
{
	function __construct($doctrine)
	{
		parent::__construct($doctrine);
		
		$this->addValidCriteria('country');
		$this->addValidCriteria('state');
		// set default status filter to active
		$this->defaultParams = array('status' => City::STATUS_ACTIVE, 'country' => 1, 'state' => 0);
	
		//manually inject service for serviceDependencies
		$this->serviceDependencies = array('services.location');
	}
	
    function setFilterOptions()
    {
        $statuses = array(
            City::STATUS_NEW => 'New', 
            City::STATUS_ACTIVE => 'Active', 
            City::STATUS_INACTIVE => 'Inactive', 
            self::FILTER_KEY_ALL => self::FILTER_LABEL_ALL
        );

        $this->setStatusFilterOption($statuses);
        $this->setCountryFilterOption();
        $this->setStateFilterOption();
    }

    function setCountryFilterOption()
    {
        // Set The Filter Option
        $countries = $this->getInjectedDependcy('services.location')->getGlobalCountries();
        foreach($countries['data'] as $each) {
            $options[$each['id']] = $each['name'];
        }

        $this->filterOptions['country'] = array(
            'label' => 'Country',
            'selected' => $this->queryParams['country'],
            'options' => $options
        );
    }
    
    function setStateFilterOption()
    {
        // Set The Filter Option
        $params = array('country_id' => $this->queryParams['country']);
        $states = $this->getInjectedDependcy('services.location')->getGlobalStates($params);
        foreach($states['states'] as $each) {
            $options[$each['id']] = $each['name'];
        }
    
        $this->filterOptions['state'] = array(
            'label' => 'State',
            'selected' => $this->queryParams['state'],
            'options' => isset($options) ? $options : array()
        );
    }
    
    function setFilteredResults()
    {   
        $this->queryParams['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
        
        $globalCitiesParams = array('country_id' => $this->queryParams['country']);
 
        if($this->queryParams['state']) {
            $globalCitiesParams['state_id'] = $this->queryParams['state'];
        }

        if($this->queryParams['status'] == 'all') {
            $globalCitiesParams['active_only'] = 0;
 
        } else {
            $globalCitiesParams['status'] = $this->queryParams['status'];
        }

        $dataFromAPi = $this->getInjectedDependcy('services.location')->getGlobalCities($globalCitiesParams);

        $this->pager->getAdapter()->setData($dataFromAPi['cities']);
        
        $this->filteredResult = $this->pager->getResults();
    }
}