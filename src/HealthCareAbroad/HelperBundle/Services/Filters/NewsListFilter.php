<?php 
/**
 * @autor Chaztine
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class NewsListFilter extends ListFilter
{

    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }
    
    function buildQueryBuilder()
    {
        $this->queryBuilder->select('a')->from('HelperBundle:News', 'a');
    
        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        $this->queryBuilder->add('orderBy', 'a.dateCreated ASC');
    }
}