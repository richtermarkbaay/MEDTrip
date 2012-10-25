<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class SpecializationListFilter extends ListFilter
{

    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }

    function buildQueryBuilder()
    {
        $this->queryBuilder->select('c')->from('TreatmentBundle:Specialization', 'c');

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('c.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "c.$sortBy " . $this->sortOrder;            

        $this->queryBuilder->add('orderBy', $sort);
    }
}