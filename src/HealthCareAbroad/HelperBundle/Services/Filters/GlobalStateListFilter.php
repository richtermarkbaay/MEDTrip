<?php
/**
 * @autor Adelbert Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Entity\State;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Doctrine\ORM\QueryBuilder;

class GlobalStateListFilter extends ArrayListFilter
{
	function __construct($doctrine)
	{
		parent::__construct($doctrine);
		
		$this->addValidCriteria('name');
		$this->addValidCriteria('country');
		
		// set default filters
		$this->defaultParams = array('status' => State::STATUS_ACTIVE, 'country' => 17, 'state' => 0, 'name' => '');
	
		//manually inject service for serviceDependencies
		$this->serviceDependencies = array('services.location');
	}
	
    function setFilterOptions()
    {
        $statuses = array(
            State::STATUS_NEW => 'New', 
            State::STATUS_ACTIVE => 'Active', 
            State::STATUS_INACTIVE => 'Inactive', 
            self::FILTER_KEY_ALL => self::FILTER_LABEL_ALL
        );

        $this->setNameFilterOption();
        $this->setStatusFilterOption($statuses);
        $this->setCountryFilterOption();
    }
    
    function setNameFilterOption()
    {
        $this->filterOptions['name'] = array('label' => 'State Name', 'value' => $this->queryParams['name']);
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

    function setFilteredResults()
    {   
        $globalStatesParams = array(
            'country_id' => $this->queryParams['country'],
            'page' => isset($this->queryParams['page']) ? $this->queryParams['page'] : 1,
            'limit' => isset($this->queryParams['limit']) ? $this->queryParams['limit'] : $this->pagerDefaultOptions['limit']
        );

        if($this->queryParams['name']) {
            $globalStatesParams['name'] = $this->queryParams['name'];
        }

        if($this->queryParams['status'] == 'all') {
            $globalStatesParams['active_only'] = 0; 
        } else {
            $globalStatesParams['status'] = $this->queryParams['status'];
        }
        
        $dataFromAPi = $this->getInjectedDependcy('services.location')->getGlobalStates($globalStatesParams);

        $this->pager->getAdapter()->setData($dataFromAPi);
        
        $this->filteredResult = $this->pager->getResults();
    }
}