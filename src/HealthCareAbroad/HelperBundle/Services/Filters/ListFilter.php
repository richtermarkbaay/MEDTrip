<?php
/**
 * @author Adelbert D. Silla
 * @desc AbstractClass For ListFilter Classes
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Exception\ListFilterException;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\ORM\QueryBuilder;
use HealthCareAbroad\PagerBundle\Pager;

abstract class ListFilter
{
    const FILTER_KEY_ALL = 'all';
    const FILTER_LABEL_ALL = 'All';

    protected $doctrine;

    protected $criteria = array();

    protected $queryParams = array();

    protected $validCriteria = array('status');
    
    protected $defaultParams = array();
    
    protected $sortBy;

    protected $sortOrder = 'asc';

    protected $filterOptions = array();

    protected $filteredResult = array();

    protected $pager;
    
    protected $pagerAdapter;

    protected $pagerDefaultOptions = array('limit' => 20, 'page' => 1);
    
    /**
     * @var array list of services that this filter depends on
     */
    protected $serviceDependencies = array();
    
    /**
     * @var array of injected service classes
     */
    protected $injectedDependencies = array();

    /**
     * @desc Default options value for Status Filter Option
     * @var array
     */
    protected $statusFilterOptions = array(1 => 'Active', 0 => 'Inactive', self::FILTER_KEY_ALL => self::FILTER_LABEL_ALL);
    
    final public function getServiceDependencies()
    {
        return $this->serviceDependencies;
    }
    
    final public function injectDependency($serviceId, $service)
    {
        $this->injectedDependencies[$serviceId] = $service;
    }
    
    final public function getInjectedDependcy($serviceId)
    {

        if (!\array_key_exists($serviceId, $this->injectedDependencies)){
            throw ListFilterException::unregisteredServiceDependency($serviceId);
        }
        
        return $this->injectedDependencies[$serviceId];
    }

    /**
     * @desc Prepare the ListFilter object
     * @param array $queryParams
     */
    function prepare($queryParams = array())
    {
        $this->setQueryParamsAndCriteria($queryParams);

        $this->setFilterOptions();

        //$this->buildQueryBuilder();

        $this->setPager();
        
        $this->setFilteredResults();
    }

    /**
     * @desc Sets queryParams and the valid criteria
     * @param array $queryParams
     */
    function setQueryParamsAndCriteria($queryParams = array())
    {
        $this->queryParams = $queryParams;
        
        foreach($this->validCriteria as $key) {

            if(isset($queryParams[$key])) {
                if(!is_null($queryParams[$key]) && $queryParams[$key] != self::FILTER_KEY_ALL)
                    $this->criteria[$key] = $queryParams[$key];
            }
            else {
                // no param, check default first
                $this->queryParams[$key] = \array_key_exists($key, $this->defaultParams) ? $this->defaultParams[$key] : self::FILTER_KEY_ALL;
            }
        }

        if (isset($this->queryParams['sortBy'])) {
            $this->sortBy = $this->queryParams['sortBy'];
        }

        if (isset($this->queryParams['sortOrder']))
            $this->sortOrder = $this->queryParams['sortOrder'];
    }

    /**
     * @desc Sets Status Filter Option
     */
    function setStatusFilterOption($statusFilterOptions = array())
    {
        if(count($statusFilterOptions))
            $this->statusFilterOptions = $statusFilterOptions;
    
        $this->filterOptions['status'] = array(
            'label' => 'Status',
            'selected' => $this->queryParams['status'],
            'options' => $this->statusFilterOptions
        );
    }

    /**
     * @desc Add a new valid criteria
     * @param string $val
     */
    function addValidCriteria($val)
    {
        array_push($this->validCriteria, $val);
    }

    /**
     * @return multitype:array
     */
    function getFilterOptions()
    {
        return $this->filterOptions;
    }

    function setPager()
    {
        $params['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
        $params['limit'] = isset($this->queryParams['limit']) ? $this->queryParams['limit'] : $this->pagerDefaultOptions['limit'];

        $this->pager = new Pager($this->pagerAdapter, $params);
    }

    /**
     * @return multitype:array object
     */
    function getFilteredResult()
    {
        return $this->filteredResult;
    }

    function getPager()
    {
        return $this->pager;
    }
}