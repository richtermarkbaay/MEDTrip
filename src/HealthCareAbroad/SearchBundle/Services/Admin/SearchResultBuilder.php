<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

abstract class SearchResultBuilder
{
    
    
    public function search(array $criteria)
    {
        $queryBuilder = $this->buildQueryBuilder($criteria);
        
        $pager = new Pager();
        $pager->setQueryBuilder($queryBuilder);
        
        $results = $pager->getResults();
        
        $arr = array();
        
        foreach ($results as $ea) {
            $arr[] = $this->buildResult($ea);
        }
        
        return $arr;
    }
    
    abstract protected function buildQueryBuilder();
    
    abstract protected function buildResult();
    
    
}