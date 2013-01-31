<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
class TreatmentsSearchResultBuilder extends SearchResultBuilder
{

    protected function buildQueryBuilder($criteria)
    {
    	$this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
    	$this->queryBuilder->select('a')->from('TreatmentBundle:Treatment', 'a');
        $this->queryBuilder->andWhere('a.name LIKE :name');
        $this->queryBuilder->setParameter('name', '%'.\trim($criteria['term']).'%');
    	
    	return $this->queryBuilder;
    }
    
    protected function buildResult($val)
    {
        $result = new AdminSearchResult();
        $result->setId($val->getId());
        $result->setDescription($val->getDescription());
        $route = $this->router->generate("admin_treatment_edit",array('id' => $val->getId()));
        $result->setUrl($route);
        $result->setName($val->getName());
        
        return $result;
    }
}