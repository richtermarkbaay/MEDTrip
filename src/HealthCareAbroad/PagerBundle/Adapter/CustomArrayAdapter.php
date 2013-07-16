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
    protected $cursor = 0;

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
     * {@inheritDoc}
     */
    function countResults($offset = null, $limit = null) {
        return $this->totalItems;
    }

    public function getResults($offset, $limit)
    {
        return $this->array;
    }

    public function isEmpty()
    {
        return empty($this->array);
    }

    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->array[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->array[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);

        $this->totalItems = null;
    }

    public function current()
    {
        return $this->offsetGet($this->cursor);
    }

    public function key()
    {
        return $this->cursor;
    }

    public function next()
    {
        $this->cursor++;
    }

    public function rewind()
    {
        $this->cursor = 0;
    }

    public function valid()
    {
        return $this->offsetExists($this->cursor);
    }

    public function count()
    {
        return $this->getTotalResults();
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
     * Get the array
     * @return array
     */
    public function getArray() {
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

    