<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;
use Doctrine\Bundle\DoctrineBundle\Registry;
class DoctorSearchResultBuilder extends SearchResultBuilder
{
	protected $doctrine;
	public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine)
	{
		$this->doctrine = $doctrine;
	}
    protected function buildQueryBuilder($criteria)
    {
    	
//     	$this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
//     	$this->queryBuilder->select('a')->from('DoctorBundle:Doctor', 'a');
//     	$this->queryBuilder->andWhere('a.firstName LIKE :seachTerm OR a.middleName LIKE :seachTerm OR a.lastName LIKE :seachTerm');
//     	$this->queryBuilder->setParameter('seachTerm', '%'.$criteria['term'].'%');
    }
    
    protected function buildResult()
    {
        $result = new AdminSearchResult();
        $result->setName();
    }
}