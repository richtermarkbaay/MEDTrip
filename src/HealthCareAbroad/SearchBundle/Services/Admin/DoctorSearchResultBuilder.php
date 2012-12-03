<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
class DoctorSearchResultBuilder extends SearchResultBuilder
{

    protected function buildQueryBuilder($criteria)
    {
    	
    	$this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();//doctrine->getEntityManager()->createQueryBuilder();
    	$this->queryBuilder->select('a')->from('DoctorBundle:Doctor', 'a');
    	$this->queryBuilder->andWhere('a.firstName LIKE :seachTerm OR a.middleName LIKE :seachTerm OR a.lastName LIKE :seachTerm');
    	$this->queryBuilder->setParameter('seachTerm', '%'.$criteria['term'].'%');
    	
    }
    
    protected function buildResult()
    {
        $result = new AdminSearchResult();
        $result->setName();
    }
}