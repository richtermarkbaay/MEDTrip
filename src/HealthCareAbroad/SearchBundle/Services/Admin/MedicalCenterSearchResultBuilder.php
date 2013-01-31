<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

class MedicalCenterSearchResultBuilder extends SearchResultBuilder
{
    protected function buildQueryBuilder($criteria)
    {
    	$this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
    	$this->queryBuilder->select('a')->from('InstitutionBundle:InstitutionMedicalCenter', 'a');
    	$this->queryBuilder->innerJoin('a.institution', 'b');
    	$this->queryBuilder->innerJoin('a.institutionSpecializations', 'c');
    	$this->queryBuilder->innerJoin('c.specialization', 'd');
    	$this->queryBuilder->where('a.name LIKE :name OR d.name LIKE :specializationName OR b.name LIKE :institutionName');
    	$this->queryBuilder->setParameter('name', '%'.\trim($criteria['term']).'%');
    	$this->queryBuilder->setParameter('specializationName', '%'.\trim($criteria['term']).'%');
    	$this->queryBuilder->setParameter('institutionName', '%'.\trim($criteria['term']).'%');
    	
    	return $this->queryBuilder;
    	exit;
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
        $route = $this->router->generate("admin_institution_medicalCenter_view",array('institutionId' => $val->getInstitution()->getId(), 'imcId' => $val->getId()));
        $result->setUrl($route);

    	return $result;
    }
}