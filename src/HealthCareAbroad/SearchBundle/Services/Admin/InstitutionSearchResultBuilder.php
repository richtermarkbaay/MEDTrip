<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
class InstitutionSearchResultBuilder extends SearchResultBuilder
{
    protected function buildQueryBuilder($criteria)
    {
    	$this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
    	$this->queryBuilder->select('a')->from('InstitutionBundle:Institution', 'a');
        $this->queryBuilder->andWhere('a.name LIKE :name');
        $this->queryBuilder->setParameter('name', '%'.$criteria['term'].'%');
    	
    	return $this->queryBuilder;
    }
    
    protected function buildResult($val)
    {
        $result = new AdminSearchResult();
        $result->setId($val->getId());
        $result->setDescription($val->getDescription());
        $result->setUrl("/admin/institution/{$val->getId()}/view");
        $result->setName($val->getName());
        
        return $result;
    }
}