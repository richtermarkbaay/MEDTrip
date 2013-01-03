<?php
namespace HealthCareAbroad\SearchBundle\Services\SearchStrategy;

use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;
use Symfony\Component\DependencyInjection\ContainerAware;

abstract class SearchStrategy extends ContainerAware
{
    const RESULT_TYPE_ARRAY = 0;
    const RESULT_TYPE_OBJECT = 1;
    const RESULT_TYPE_INTERNAL = 2;

    protected $results;

    protected $resultType;

    protected $isViewReadyResults;

    abstract public function isAvailable();

    /**
     *
     * @param SearchParameterBag $searchParam
     */
    abstract public function search(SearchParameterBag $searchParam);

    abstract public function getArrayResults();

    abstract public function getObjectResults();

    public function getInternalResults()
    {
        return $this->resultType;
    }

    /**
     *
     * @param int $resultType
     */
    public function setResultType($resultType)
    {
        $this->resultType = $resultType;
    }

    public function isViewReadyResults()
    {
        return $this->isViewReadyResults;
    }
}