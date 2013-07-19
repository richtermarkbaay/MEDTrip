<?php 
/**
 * 
 * @author Adelbert Silla
 *
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;
use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementStatuses;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

class AdvertisementListFilter extends DoctrineOrmListFilter
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

        $statusOptions = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL) + AdvertisementStatuses::getList();
        $this->setStatusFilterOption($statusOptions);        
    }

    function setAdvertisementTypeFilterOption()
    {        
        // Set The Filter Option 
        $advertisementTypes = $this->doctrine->getRepository('AdvertisementBundle:AdvertisementType')->findByStatus(1);
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
     
        foreach($advertisementTypes as $each) {
            $options[$each->getId()] = $each->getName();
        }

        $this->filterOptions['advertisementType'] = array(
            'label' => 'Advertisement Types',
            'selected' => $this->queryParams['advertisementType'],
            'options' => $options
        );
    }

    function setFilteredResults()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
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
    	
    	$this->pagerAdapter->setQueryBuilder($this->queryBuilder);
    	
    	$this->filteredResult = $this->pager->getResults();
    }
}