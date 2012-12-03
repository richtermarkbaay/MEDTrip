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
    	return $this->queryBuilder;
    }
    
    protected function buildResult($val)
    {
//     	echo "<pre>";
// 		   print_r($val);
// 		echo "</pre>";
// 		exit;
        $result = new AdminSearchResult();
        $result->setId($val->getId());
        $result->setFirstName($val->getFirstName());
        $result->setLastName($val->getLastName());
        $result->setMiddleName($val->getMiddleName());
        $result->setUrl("/admin/doctor/edit/{$val->getId()}");
        
//         $result->setUrl($val->getUrl());
    }
}