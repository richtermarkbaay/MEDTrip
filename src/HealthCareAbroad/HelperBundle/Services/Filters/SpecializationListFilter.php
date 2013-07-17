<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

class SpecializationListFilter extends DoctrineOrmListFilter
{
    public function __construct($doctrine)
    {
        parent::__construct($doctrine);

        $this->defaultParams = array('status' => Specialization::STATUS_ACTIVE);
    }

    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }

    function setFilteredResults()
    {
        $queryBuilder = $this->pager->getAdapter()->getQueryBuilder();

        $queryBuilder->select('c')->from('TreatmentBundle:Specialization', 'c');

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $queryBuilder->where('c.status = :status');
            $queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "c.$sortBy " . $this->sortOrder;            

        $queryBuilder->add('orderBy', $sort);

        $this->pager->getAdapter()->setQueryBuilder($queryBuilder);

        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}