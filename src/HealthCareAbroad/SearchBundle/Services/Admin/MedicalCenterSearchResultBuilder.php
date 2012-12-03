<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

class MedicalCenterSearchResultBuilder extends SearchResultBuilder
{
    protected function buildQueryBuilder($criteria)
    {
    	$this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
    	$this->queryBuilder->select('a')->from('InstitutionBundle:InstitutionMedicalCenter', 'a');
    	$this->queryBuilder->join('a.institution', 'b');
    	$this->queryBuilder->join('a.institutionSpecializations', 'c');
    	$this->queryBuilder->innerJoin('c.specialization', 'd');
    	$this->queryBuilder->where('a.institution = b.id');
    	$this->queryBuilder->andWhere('a.id = c.institutionMedicalCenter');
    	$this->queryBuilder->andWhere('c.specialization = d.id');
    	$this->queryBuilder->andWhere('a.name LIKE :name');
    	$this->queryBuilder->setParameter('name', '%'.$criteria['term'].'%');
    	
    	return $this->queryBuilder;
    }
    
    protected function buildResult($val)
    {	
    	// for specialization
//     	$specialization = array();
//     	foreach ($val->getInstitutionSpecializations() as $v){
//     		$re = $v->getSpecialization();
//     		$specialization[] = $re->getName();
//     	}

        $result = new AdminSearchResult();
        $result->setId($val->getId());
        $result->setName($val->getName());
        $result->setDescription("Medical Center Name : {$val->getDescription()} Institution Name: {$val->getInstitution()->getName()}");
        $result->setUrl("admin/institution/{$val->getInstitution()->getId()}/medical-centers/{$val->getId()}/edit");

    	return $result;
    }
}