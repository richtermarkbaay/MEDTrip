<?php
/**
 * @author Adelbert Silla
 */
namespace HealthCareAbroad\PagerBundle\Adapter;

/**
 * Pager NativeQuery adapter interface
 *
 */
interface PagerNativeQueryAdapterInterface extends PagerAdapterInterface
{
    /**
     * This function sets the main query, query to get total count and query parameters.
     * This also reconstract the queries with the given parameters and sort order. 
     * 
     * @param string $query Main Query
     * @param string $countQuery Total Count Query
     * @param array $queryParams
     * @param string $sort Sort 
     */
    function setQueriesAndQueryParams($query, $countQuery, array $queryParams, $sort);
}