<?php
/**
 * @author Adelbert D. Silla
 * @desc NativeQuery AbstractClass For ListFilter Classes
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;


use HealthCareAbroad\PagerBundle\Adapter\NativeQueryAdapter;

use Doctrine\DBAL\Connection;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

abstract class NativeQueryListFilter extends ListFilter {

    abstract function setFilterOptions();
    
    public function __construct(Connection $connection)
    {
        $this->pagerAdapter = new NativeQueryAdapter($connection);
    }
}