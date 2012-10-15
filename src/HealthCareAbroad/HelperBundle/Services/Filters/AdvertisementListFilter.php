<?php 
/**
 * @autor Chaztine Blance
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;
use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

class AdvertisementListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add advertisementType in validCriteria
        $this->addValidCriteria('advertisementTypes');
    }

    function setFilterOptions()
    {
        $this->setAdvertisementTypeFilterOption();

        $this->setStatusFilterOption();
        
    }

    function setAdvertisementTypeFilterOption()
    {        
        // Set The Filter Option 
        $advertisementTypes = AdvertisementTypes::getList();
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
     
        foreach($advertisementTypes as $each => $m) {
            $options[$each] = $m;
        }

        $this->filterOptions['advertisementTypes'] = array(
            'label' => 'Advertisement Types',
            'selected' => $this->queryParams['advertisementTypes'],
            'options' => $options
        );
    }

    function buildQueryBuilder()
    {
    	if ($this->queryParams['advertisementTypes'] != ListFilter::FILTER_KEY_ALL) {
    	
    		$this->queryBuilder = $this->doctrine->getRepository("AdvertisementBundle:Advertisement")->getQueryBuilderForAdvertisementsByType($this->queryParams['advertisementTypes']);
    		
    	}else{
    		
    		$this->queryBuilder->select('a')->from('AdvertisementBundle:Advertisement', 'a');

    	}

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }	
        
    }
}