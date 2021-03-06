<?php
/*
 * This file is part of the PagerBundle package.
 *
 */
namespace HealthCareAbroad\PagerBundle\Adapter;

use HealthCareAbroad\PagerBundle\Adapter\PagerAdapterInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * DoctrineOrmAdapter
 *
 */
class DoctrineOrmAdapter implements PagerAdapterInterface, \Countable
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var string
     */
    protected $hydrationMode;

    /**
     * @var int
     */
    protected $totalResults;

    /**
     * @var array
     */
    protected $countCache = array();

    public function __construct(QueryBuilder $queryBuilder = null, $hydrationMode = null)
    {
        $this->setQueryBuilder($queryBuilder)
             ->setHydrationMode($hydrationMode);
    }

    /**
     * Returns the count query instance
     * WARNING!!!!
     *    This will REMOVE ALL GROUP BY clause. If count query produced different result from what is expected, 
     *    please extend this Adapter and override  getCountQuery method.
     * @return QueryBuilder
     */
    public function getCountQuery($offset = null, $limit = null)
    {
        $queryBuilder = $this->getQueryBuilder();
        if(null === $queryBuilder) {
            throw new \Exception('No queryBuilder is set for this adapter.');
        }

        $aliases = $queryBuilder->getRootAliases();
        
        $alias = $aliases[0];

        $qb = clone $queryBuilder;
        $groupBy = $qb->getDQLPart('groupBy');
        if (\is_array($groupBy) && \count($groupBy) > 0 ){
            $qb->resetDQLPart('groupBy');
            $qb->select('COUNT( DISTINCT ' . $alias . ')');
            
        }
        else {
            $qb->select('COUNT(' . $alias . ')');
        }

        return $qb->resetDQLPart('orderBy')->setMaxResults($limit)->setFirstResult($offset);
        
    }

    /**
     * Returns the total number of results
     *
     * @return int
     */
    public function getTotalResults()
    {
        if (null === $this->totalResults) {
            $this->totalResults = $this->countResults();
        }
        return $this->totalResults;
    }

    /**
     * Count results
     * @param int|null $offset
     * @param int|null $limit
     * @return int
     */
    public function countResults($offset = null, $limit = null) {
        if(null === $this->getQueryBuilder()) {
            return 0;
        }

        if(isset($this->countCache[$offset."-".$limit])) {
            return $this->countCache[$offset."-".$limit];
        }
        return $this->getCountQuery($offset, $limit)->getQuery()->getSingleScalarResult();
    }

    /**
     * {@inheritDoc}
     */
    public function count() {
        return $this->getTotalResults();
    }

    /**
     * Returns the list of results
     *
     * @return array
     */
    public function getResults($offset, $limit)
    {
        return $this->queryBuilder->setFirstResult($offset)->setMaxResults($limit)->getQuery()->execute(array(), $this->getHydrationMode());
    }

    /**
     * Set the hydation mode
     * @param string $hydrationMode
     * @return DoctrineOrmAdapter Provides a fluent interface
     */
    public function setHydrationMode($hydrationMode) {
        $this->hydrationMode = $hydrationMode;
        return $this;
    }

    /**
     * Get the hydration mode
     * @return string
     */
    public function getHydrationMode() {
        return $this->hydrationMode;
    }

    /**
     * Set a new QueryBuilder
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return DoctrineOrmAdapter Provides a fluent interface
     */
    public function setQueryBuilder($queryBuilder) {
        $this->queryBuilder = $queryBuilder;
        $this->countCache = array();
        $this->totalResults = null;
        return $this;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder() {
        return $this->queryBuilder;
    }
}
