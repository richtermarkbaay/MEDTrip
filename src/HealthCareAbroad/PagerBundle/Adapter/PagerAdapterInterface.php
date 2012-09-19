<?php
/*
 * This file is part of the PagerBundle package.
 *
 */
namespace HealthCareAbroad\PagerBundle\Adapter;

/**
 * Pager adapter interface
 *
 */
interface PagerAdapterInterface
{
    /**
     * Returns the list of results
     *
     * @return array
     */
    function getResults($offset, $limit);

    /**
     * Returns the total number of results
     *
     * @return integer
     */
    function getTotalResults();

    /**
     * Returns the total number of results for the current offset & limit
     */
    function countResults($offset = null, $limit = null);
}
