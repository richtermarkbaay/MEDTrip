<?php 
/**
 * 
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;
use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

class AdvertisementListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add advertisementType in validCriteria
        $this->addValidCriteria('advertisementType');
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

        $this->filterOptions['advertisementType'] = array(
            'label' => 'Advertisement Types',
            'selected' => $this->queryParams['advertisementType'],
            'options' => $options
        );
    }

    function buildQueryBuilder()
    {
        $this->queryBuilder->select('a')->from('AdvertisementBundle:Advertisement', 'a');

    	if ($this->queryParams['advertisementType'] != ListFilter::FILTER_KEY_ALL) {
	         $this->queryBuilder->where('a.advertisementType = :advertisementType');
	         $this->queryBuilder->setParameter('advertisementType', $this->queryParams['advertisementType']);
    	}

    	if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
    		$this->queryBuilder->andWhere('a.status = :status');
    		$this->queryBuilder->setParameter('status', $this->queryParams['status']);
    	}

    	if($this->sortBy == 'title') {
    		$sort = 'a.title ' . $this->sortOrder;
    	} else {
    		$sortBy = $this->sortBy ? $this->sortBy : 'title';
    		$sort = "a.$sortBy " . $this->sortOrder;
    	}
    	
    	$this->queryBuilder->add('orderBy', $sort);
    	
    }
}