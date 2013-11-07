<?php
/*
 * This file is part of the PagerBundle package.
 *
 */
namespace HealthCareAbroad\PagerBundle\Adapter;

use HealthCareAbroad\PagerBundle\Adapter\PagerAdapterInterface;

/**
 * Pager array adapter
 *
 */
class CustomArrayAdapter implements PagerAdapterInterface
{
    /**
     * @var array
     */
    protected $array;

    /**
     * @var int
     */
    protected $totalItems;

    public function __construct(array $data = array())
    {
        if(!empty($data)) {
            $this->setData($data);
        }
    }

    /**
     * Set the array and totalItems
     * @param $array
     * @return ArrayAdapter Provides a fluent interface
     */
    public function setData(array $data) {
        $this->array = $data;
        $this->totalItems = count($data);
    
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    function countResults($offset = null, $limit = null) {
        return count(array_slice($this->array, $offset, $limit));
    }

    public function getResults($offset, $limit)
    {
        return array_slice($this->array, $offset, $limit);
    }
    
    /**
     * Get totalItems
     * @return integer
     */
    public function getTotalResults() {
        
        return $this->totalItems;
    }
}

    