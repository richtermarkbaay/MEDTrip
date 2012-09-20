<?php
/**
 * @author Adelbert D. Silla
 * @desc AbstractClass For ListFilter Classes
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\ORM\QueryBuilder;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

abstract class ListFilter
{
    const FILTER_KEY_ALL = 'all';
    const FILTER_LABEL_ALL = 'All';

    protected $doctrine;

    protected $entityRepository;

    protected $criteria = array();

    protected $queryParams = array();

    protected $validCriteria = array('status');
    
    protected $sortBy;

    protected $sortOrder = 'asc';

    protected $filterOptions = array();

    protected $filteredResult = array();

    protected $queryBuilder;

    protected $pager;

    protected $pagerDefaultOptions = array('limit' => 10, 'page' => 1);

    /**
     * @desc Default options value for Status Filter Option
     * @var array
     */
    protected $statusFilterOptions = array(1 => 'Active', 0 => 'Inactive', self::FILTER_KEY_ALL => self::FILTER_LABEL_ALL);

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
        $this->queryBuilder = $doctrine->getEntityManager()->createQueryBuilder();
    }

    abstract function setFilterOptions();

    abstract function buildQueryBuilder();

    /**
     * @desc Prepare the ListFilter object
     * @param array $queryParams
     */
    function prepare($queryParams = array())
    {
        $this->setQueryParamsAndCriteria($queryParams);

        $this->setFilterOptions();

        $this->buildQueryBuilder();

        $this->setPager();

        $this->setFilteredResult();
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

            else $this->queryParams[$key] = self::FILTER_KEY_ALL;
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
        $adapter = new DoctrineOrmAdapter($this->queryBuilder);

        $params['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
        $params['limit'] = isset($this->queryParams['limit']) ? $this->queryParams['limit'] : $this->pagerDefaultOptions['limit'];

        $this->pager = new Pager($adapter, $params);
    }

    function setFilteredResult()
    {
        $this->filteredResult = $this->pager->getResults();
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