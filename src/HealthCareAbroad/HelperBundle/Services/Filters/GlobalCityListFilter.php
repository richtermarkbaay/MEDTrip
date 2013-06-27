<?php
/**
 * @autor Alnie Jacobe
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Entity\City;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Doctrine\ORM\QueryBuilder;

class GlobalCityListFilter extends ListFilter
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
    
    function buildQueryBuilder()
    {   
        
//         if($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL && $this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
//             $cityList = $this->getInjectedDependcy('services.location')->getGlobalCityListByStatusAndCountry($this->queryParams['status']);
//         }
//         elseif($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
//             $cityList = $this->getInjectedDependcy('services.location')->getGlobalCityListByStatus($this->queryParams['status']);
//         }
//         elseif($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
//             $cityList = $this->getInjectedDependcy('services.location')->getGlobalCityListByCountry($this->queryParams['country']);
//         }
//         else {
//             $cityList = $this->getInjectedDependcy('services.location')->getGlobalCityList();
//         }
        $cityList = $this->getInjectedDependcy('services.location')->getGlobalCityList();
        // TODO: Temparary fix for pager array adapter type.
        $this->queryBuilder = $cityList;        
    }
}