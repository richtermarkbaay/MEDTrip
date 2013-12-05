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
        $this->array = $data['data'];

        $this->totalItems = $data['totalResults'];

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    function countResults($offset = null, $limit = null) {
        return count($this->array);
    }

    public function getResults($offset, $limit)
    {
        return $this->array;
    }
    
    /**
     * Get totalItems
     * @return integer
     */
    public function getTotalResults() {
        
        return $this->totalItems;
    }
}

    