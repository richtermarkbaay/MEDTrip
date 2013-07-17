<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class CityListFilter extends DoctrineOrmListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add country in validCriteria
        $this->addValidCriteria('country');
    }

    function setFilterOptions()
    {
        $this->setCountryFilterOption();

        $this->setStatusFilterOption();
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
        $queryBuilder = $this->pager->getAdapter()->getQueryBuilder();
        
        $queryBuilder->select('a')->from('HelperBundle:City', 'a');

        if ($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
            $queryBuilder->where('a.country = :country');
            $queryBuilder->setParameter('country', $this->queryParams['country']);
        }

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $queryBuilder->andWhere('a.status = :status');
            $queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        if($this->sortBy == 'country') {
            $queryBuilder->leftJoin('a.country', 'b');
            $sort = 'b.name ' . $this->sortOrder;
        } else {
            $sortBy = $this->sortBy ? $this->sortBy : 'name';
            $sort = "a.$sortBy " . $this->sortOrder;            
        }

        $queryBuilder->add('orderBy', $sort);

        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}