<?php
/*
 * This file is part of the PagerBundle package.
 *
 */
namespace HealthCareAbroad\PagerBundle\Adapter;

use Doctrine\DBAL\Connection;

use HealthCareAbroad\PagerBundle\Adapter\PagerAdapterInterface;

/**
 * DoctrineOrmAdapter
 *
 */
class NativeQueryAdapter implements PagerNativeQueryAdapterInterface, \Countable
{
    /**
     * @var Doctrine\DBAL\Connection;
     */
    protected $connection;

    /** 
     * @var string
     */
    protected $query;

    /**
     * @var string
     */
    protected $countQuery;
    
    /** 
     * @var array
     */
    protected $queryParams;

    /**
     * @var array
     */
    protected $results;

    /**
     * @var int
     */
    static protected $totalResults;

    /**
     * @var array
     */
    protected $countCache = array();

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Returns the total number of results
     *
     * @return int
     */
    public function getTotalResults()
    {
        if(!static::$totalResults) {
            $statement = $this->prepareStatement($this->countQuery);
            $statement->execute($this->queryParams);
            
            static::$totalResults = $statement->rowCount() > 1 ? $statement->rowCount() : $statement->fetchColumn(0);
        }

        return static::$totalResults;
    }

    /**
     * Count results
     * @param int|null $offset
     * @param int|null $limit
     * @return int
     */
    public function countResults($offset = null, $limit = null) 
    {
        return \count($this->results);
    }

    /**
     * {@inheritDoc}
     */
    public function count() 
    {
        return \count($this->results);
    }

    /**
     * Returns the list of results
     *
     * @return array
     */
    public function getResults($offset, $limit, $params = array())
    { 
        $statement = $this->prepareStatement($this->query, $offset, $limit);
        $statement->execute($this->queryParams);

        $this->results = $statement->fetchAll();

        return $this->results;
    }
    
    public function setQueriesAndQueryParams($query, $countQuery, array $queryParams = array(), $sort, $groupBy = '')
    {
        $newQueryParams = array();
        $this->queryParams = $queryParams;

        $sort = " ORDER BY $sort";
        
        if($groupBy) {
            $groupBy = " GROUP BY $groupBy ";
        }

        if(empty($queryParams)) {
            $this->query = $query . $groupBy . $sort;
            $this->countQuery = $countQuery . $groupBy;

            return;
        }

        $operator = "=";
        $subQuery = " WHERE ";

        foreach($queryParams as $key => $value) {
            $keyParam = str_replace('.', '', $key);

            if(is_array($value)) {
                $operator = key($value);

                if(strtolower($operator) == 'between') {
                    
                    $newQueryParams[':'. $keyParam . '_from'] = $value[$operator][0];
                    $newQueryParams[':'. $keyParam . '_to'] = $value[$operator][1];
                    $subQuery .= "$key $operator :$keyParam" . "_from AND :" . $keyParam . "_to AND ";

                } else {
                    $newQueryParams[":$keyParam"] = $value[$operator];
                    $subQuery .= "$key $operator :$keyParam AND ";
                }

            } else {
                $newQueryParams[":$keyParam"] = $value;
                $subQuery .= "$key = :$keyParam AND ";                
            }            
        }

        $subQuery = substr($subQuery, 0, -4);

        $this->query = $query . $subQuery . $groupBy . $sort;

        $this->countQuery = $countQuery . $subQuery . $groupBy;
        $this->queryParams = $newQueryParams;
    }

    /** 
     * @param string $query
     * @param array$params
     * @param int $offset
     * @param int $limit
     * @return Doctrine\DBAL\Statement
     */
    private function prepareStatement($query, $offset = null, $limit = null)
    {
        if($limit) {
            $query .= " LIMIT $offset, $limit";
        }

        return $this->connection->prepare(\trim($query));
    }
}