<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
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
        $result = new AdminSearchResult();
        $result->setId($val->getId());
        $result->setFirstName($val->getFirstName());
        $result->setLastName($val->getLastName());
        $result->setMiddleName($val->getMiddleName());
        var_dump($this->router);exit;
        //$router->generate('admin_doctor_edit', array('id' => ))
        $result->setUrl("/admin/doctor/edit/{$val->getId()}");
        $result->setName($result->getFullName());
        return $result;
    }
}