<?php
namespace HealthCareAbroad\HelperBundle\Services\Helpers;

use Doctrine\ORM\QueryBuilder;

class QueryHelper
{
    const DB_DATE_FORMAT = 'Y-m-d';
    
    const DB_DATETIME_FORMAT = 'Y-m-d h:i:s';
    
    /**
     * Convenience function to apply $filters to a QueryBuilder. $filters will be checked if it belongs to $knownFilters
     *
     * @param QueryBuilder $qb
     * @param array $knownFilters
     * @param array $filters
     * @return QueryBuilder
     */
    static public function applyQueryBuilderFilters(QueryBuilder $qb, array $knownFilters, array $filters=array())
    {
        foreach ($filters as $key => $val){
            if (isset($knownFilters[$key]) && !\is_null($val)){
                $expr = $knownFilters[$key];
                $qb->andWhere($expr)
                ->setParameter($key, $val);
            }
        }
    
        return $qb;
    }    
}