<?php
/**
 * @autor Chaztine Blance
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Entity\AwardingBody;

class GlobalAwardListFilter extends DoctrineOrmListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add country in validCriteria
        $this->addValidCriteria('awardingBody');
        $this->addValidCriteria('country');
        $this->addValidCriteria('name');
    }

    function setFilterOptions()
    {
        $this->setAwardingBodiesFilterOption();
        $this->setCountryFilterOption();
        $this->setStatusFilterOption();
        $this->setNameFilterOption();
    }
    
    function setNameFilterOption()
    {
        if($this->queryParams['name'] == 'all') {
            $this->queryParams['name'] = '';
        }
    
        $this->filterOptions['name'] = array('label' => 'Global Award Name', 'value' => isset($this->queryParams['name']) ? $this->queryParams['name'] : '' );
    }

    function setAwardingBodiesFilterOption()
    {
        // Set The Filter Option
        $awardingBodies = $this->doctrine->getEntityManager()->getRepository('HelperBundle:AwardingBody')->findBy(array('status'=> AwardingBody::STATUS_ACTIVE),array('name' => 'ASC'));
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        
 
        foreach($awardingBodies as $each) {
            $options[$each->getId()] = $each->getName();
        }

        $this->filterOptions['awardingBody'] = array(
            'label' => 'Awarding Body',
            'selected' => $this->queryParams['awardingBody'],
            'options' => $options
        );
    }
    
    function setCountryFilterOption()
    {
    	// Set The Filter Option
    	$countries = $this->doctrine->getEntityManager()->getRepository('HelperBundle:Country')->findByStatus(1);
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

    function setFilteredResults()
    {   
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('HelperBundle:GlobalAward', 'a');

        if ($this->queryParams['awardingBody'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.awardingBody = :awardingBody');
            $this->queryBuilder->setParameter('awardingBody', $this->queryParams['awardingBody']);
        }

        if ($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
        	$this->queryBuilder->where('a.country = :country');
        	$this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }
        
        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
        
        if($this->sortBy == 'country') {
        	$this->queryBuilder->leftJoin('a.country', 'b');
        	$sort = 'b.name ' . $this->sortOrder;
        }
        
        if (trim($this->queryParams['name'])) {
            $this->queryBuilder->andWhere('a.name LIKE :name');
            $this->queryBuilder->setParameter('name', "%" . $this->queryParams['name'] . "%");
        }
        
        if($this->sortBy == 'awardingBody') {
            $this->queryBuilder->leftJoin('a.awardingBody', 'b');
            $sort = 'b.name ' . $this->sortOrder;
        } else {
            $sortBy = $this->sortBy ? $this->sortBy : 'name';
            $sort = "a.$sortBy " . $this->sortOrder;            
        }

        $this->queryBuilder->add('orderBy', $sort);
        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}