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
		
		// TODO: Temparary fix for pager array adapter type.
		$this->dataType = 'array';
		$this->addValidCriteria('country');
		// set default status filter to active
		$this->defaultParams = array('status' => City::STATUS_ACTIVE);
	
		//manually inject service for serviceDependencies
		$this->serviceDependencies = array('services.location');
	}
	
    function setFilterOptions()
    {
        $this->setStatusFilterOption();
        //$this->setCountryFilterOption();
    }

    function setCountryFilterOption()
    {
        // Set The Filter Option
        $countries = $this->doctrine->getEntityManager()->getRepository('HelperBundle:Country')->findAll();
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($countries as $each) {
            $options[$each->getId()] = $each->getName();
        }
    
        $this->filterOptions['country'] = array(
                        'label' => 'Country',
                        'selected' => $this->queryParams['country'],
                        'options' => $options
        );
    }
    
    function setFilterResults()
    {   
        $cityList = $this->getInjectedDependcy('services.location')->getGlobalCityList();

        $this->pager->getAdapter()->setArray($cityList);

        $this->filteredResult = $this->pager->getResults();
    }
}