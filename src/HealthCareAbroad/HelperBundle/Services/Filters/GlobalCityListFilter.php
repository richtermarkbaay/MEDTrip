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
		// set default status filter to active
		$this->defaultParams = array('status' => City::STATUS_ACTIVE);
	
		//manually inject service for serviceDependencies
		$this->serviceDependencies = array('services.location');
	}
	
    function setFilterOptions()
    {
        $this->setStatusFilterOption();
        $this->setCountryFilterOption();
    }

    function setCountryFilterOption()
    {
        // Set The Filter Option
        $countries = $this->getInjectedDependcy('services.location')->getAllGlobalCountries();
        foreach($countries as $each) {
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
        $this->queryParams['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
        
        $dataFromAPi = $this->getInjectedDependcy('services.location')->getGlobalCities($this->queryParams);

        $this->pager->getAdapter()->setData($dataFromAPi);
        
        $this->filteredResult = $this->pager->getResults();
    }
}