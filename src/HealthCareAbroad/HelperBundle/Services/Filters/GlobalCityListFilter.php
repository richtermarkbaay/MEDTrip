<?php
/**
 * @autor Adelbert Silla
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
		
		$this->addValidCriteria('name');
		$this->addValidCriteria('country');
		$this->addValidCriteria('state');

		// set default filters
		$this->defaultParams = array('status' => City::STATUS_ACTIVE, 'country' => 17, 'state' => self::FILTER_KEY_ALL, 'name' => '');
	
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

        $this->setNameFilterOption();
        $this->setStatusFilterOption($statuses);
        $this->setCountryFilterOption();
        $this->setStateFilterOption();
    }

    function setNameFilterOption()
    {
        $this->filterOptions['name'] = array('label' => 'City Name', 'value' => $this->queryParams['name']);
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
        $params = array('country_id' => $this->queryParams['country'], 'key_value' => 1);
        $states = $this->getInjectedDependcy('services.location')->getGlobalStates($params);

        $options = array(self::FILTER_KEY_ALL => self::FILTER_LABEL_ALL);
        foreach($states['data'] as $each) {
            $options[$each['id']] = $each['name'];
        }
    
        $this->filterOptions['state'] = array(
            'label' => 'State/Province',
            'selected' => $this->queryParams['state'],
            'options' => isset($options) ? $options : array()
        );
    }
    
    function setFilteredResults()
    {   
        $globalCitiesParams = array(
            'country_id' => $this->queryParams['country'],
            'page' => isset($this->queryParams['page']) ? $this->queryParams['page'] : 1,
            'limit' => isset($this->queryParams['limit']) ? $this->queryParams['limit'] : $this->pagerDefaultOptions['limit']
        );

        if($this->queryParams['name']) {
            $globalCitiesParams['name'] = $this->queryParams['name'];
        }

        if($this->queryParams['state'] && $this->queryParams['state'] != self::FILTER_KEY_ALL) {
            $globalCitiesParams['state_id'] = $this->queryParams['state'];
        }

        if($this->queryParams['status'] == self::FILTER_KEY_ALL) {
            $globalCitiesParams['active_only'] = 0; 
        } else {
            $globalCitiesParams['status'] = $this->queryParams['status'];
        }

        $dataFromAPi = $this->getInjectedDependcy('services.location')->getGlobalCities($globalCitiesParams);

        $this->pager->getAdapter()->setData($dataFromAPi);
        
        $this->filteredResult = $this->pager->getResults();
    }
}