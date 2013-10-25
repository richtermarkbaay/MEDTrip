<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
use HealthCareAbroad\DoctorBundle\Services\DoctorService;

class DoctorSearchResultBuilder extends SearchResultBuilder
{

    protected function buildQueryBuilder($criteria)
    {
    	$this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
    	$this->queryBuilder->select('a')->from('DoctorBundle:Doctor', 'a');
    	
        $this->queryBuilder->andWhere(
                        $this->queryBuilder->expr()->like(
                            $this->queryBuilder->expr()->concat($this->queryBuilder->expr()->concat('a.firstName', $this->queryBuilder->expr()->literal(' ')), 'a.lastName'),
                            '?1'
                        )
                    );
    	$this->queryBuilder->setParameter('1', '%'.\trim($criteria['term']).'%');
    	
    	return $this->queryBuilder;
    }
    
    protected function buildResult($val)
    {
        $result = new AdminSearchResult();
        $result->setId($val->getId());
        $result->setFirstName($val->getFirstName());
        $result->setLastName($val->getLastName());
        $result->setMiddleName($val->getMiddleName());
        $route = $this->router->generate("admin_doctor_edit",array('idId' => $val->getId()));
        $result->setUrl($route);
        $result->setName(DoctorService::getFullName($val));
        return $result;
    }
}