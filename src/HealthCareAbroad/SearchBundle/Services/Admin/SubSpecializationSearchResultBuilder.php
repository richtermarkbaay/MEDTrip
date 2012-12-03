<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
class SubSpecializationSearchResultBuilder extends SearchResultBuilder
{

    protected function buildQueryBuilder($criteria)
    {
    	$this->queryBuilder->andWhere('a.name LIKE :name');
        $this->queryBuilder->setParameter('name', '%'.$searchTerm.'%');
    	
    	return $this->queryBuilder;
    }
    
    protected function buildResult($val)
    {
        $result = new AdminSearchResult();
        $result->setId($val->getId());
        $result->setUrl("/admin/sub-specialization/edit/{$val->getId()}");
        $result->setName($val->getName());
        return $result;
    }
}