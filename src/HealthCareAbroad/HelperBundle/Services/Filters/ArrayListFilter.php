<?php
/**
 * @author Adelbert D. Silla
 * @desc AbstractClass For ListFilter Classes
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\PagerBundle\Adapter\CustomArrayAdapter;

use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;


abstract class ArrayListFilter extends ListFilter {
    
    abstract function setFilterOptions();
    
    public function __construct()
    {
        $this->pagerAdapter = new CustomArrayAdapter();
    }
}