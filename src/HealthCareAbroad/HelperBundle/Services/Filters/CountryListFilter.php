<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\ORM\QueryBuilder;

class CountryListFilter extends ListFilter
{

    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }

    function buildQueryBuilder()
    {
        $this->queryBuilder->select('c')->from('HelperBundle:Country', 'c');

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('c.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        $this->queryBuilder->add('orderBy', 'c.name ASC');
    }
}