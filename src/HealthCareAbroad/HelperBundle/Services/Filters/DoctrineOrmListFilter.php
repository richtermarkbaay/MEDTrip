<?php
/**
 * @author Adelbert D. Silla
 * @desc AbstractClass For ListFilter Classes
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;


use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

abstract class DoctrineOrmListFilter extends ListFilter {

    abstract function setFilterOptions();
    
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;

        $queryBuilder = $doctrine->getEntityManager()->createQueryBuilder();
        $this->pagerAdapter = new DoctrineOrmAdapter();
    }
}