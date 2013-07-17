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
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();

        $this->queryBuilder->select('c')->from('TreatmentBundle:Specialization', 'c');

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('c.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "c.$sortBy " . $this->sortOrder;            

        $this->queryBuilder->add('orderBy', $sort);

        $this->pager->getAdapter()->setQueryBuilder($this->queryBuilder);

        $this->filteredResult = $this->pager->getResults();
    }
}