<?php 
/**
 * Error Reports Filter
 * @author Chaztine Blance
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class ErrorReportListFilter extends ListFilter
{

    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }
    
    function buildQueryBuilder()
    {
        $this->queryBuilder->select('a')->from('AdminBundle:ErrorReport', 'a');
    
        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        $this->queryBuilder->orderBy('a.id', 'ASC');
    }
}

