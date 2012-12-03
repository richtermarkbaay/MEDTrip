<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
class TreatmentsSearchResultBuilder extends SearchResultBuilder
{

    protected function buildQueryBuilder($criteria)
    {
    	$this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
    	$this->queryBuilder->select('a')->from('TreatmentBundle:Treatment', 'a');
        $this->queryBuilder->andWhere('a.name LIKE :name');
        $this->queryBuilder->setParameter('name', '%'.$criteria['term'].'%');
    	
    	return $this->queryBuilder;
    }
    
    protected function buildResult($val)
    {
        $result = new AdminSearchResult();
        $result->setId($val->getId());
        $result->setDescription($val->getDescription());
        $result->setUrl("/admin/treatment/edit/{$val->getId()}");
        $result->setName($val->getName());
        
        return $result;
    }
}