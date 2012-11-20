<?php
/**
 * @autor Chaztine Blance
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\ORM\QueryBuilder;

class AwardingBodyListFilter extends ListFilter
{

    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }

    function buildQueryBuilder()
    {
        $this->queryBuilder->select('c')->from('HelperBundle:AwardingBody', 'c');

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('c.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
          
        }
        
        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "c.$sortBy " . $this->sortOrder;
        
        $this->queryBuilder->add('orderBy', $sort);
    }
}