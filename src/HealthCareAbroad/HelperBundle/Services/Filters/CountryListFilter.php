<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Doctrine\ORM\QueryBuilder;

class CountryListFilter extends ListFilter
{

    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }

    function setQueryBuilder()
    {
        $this->queryBuilder = new QueryBuilder($this->doctrine->getEntityManager());
        $this->queryBuilder->select('c')->from('HelperBundle:Country', 'c');

        if ($this->queryParams['status'] != 'all') {
            $this->queryBuilder->where('c.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
    }
}