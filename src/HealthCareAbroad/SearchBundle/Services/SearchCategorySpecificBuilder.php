<?php 

namespace HealthCareAbroad\SearchBundle\Services;

abstract class SearchCategorySpecificBuilder
{
    private $category = array(
                    Constants::SEARCH_CATEGORY_INSTITUTION => 'InstitutionBundle:Institution',
                    Constants::SEARCH_CATEGORY_CENTER => 'InstitutionBundle:InstitutionMedicalCenter',
                    Constants::SEARCH_CATEGORY_PROCEDURE_TYPE => 'TreatmentBundle:Treatment',
                    Constants::SEARCH_CATEGORY_DOCTOR => 'DoctorBundle:Doctor',
                    Constants::SEARCH_CATEGORY_SPECIALIZATION => 'TreatmentBundle:Specialization',
                    Constants::SEARCH_CATEGORY_SUB_SPECIALIZATION => 'TreatmentBundle:SubSpecialization');
    
    private function hydrateSearchData($results)
    {
        //doctor
        foreach ($data as $results)
        {
            echo $data;exit;
        }
        return;
    }
    
    public function getResults($searchCriteria)
    {
        $results = $this->buildQueryBuilder($searchCriteria);
        return $this->hydrateSearchData($results);
    }
    
    public function buildQueryBuilder(array $searchCriteria = array())
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from($this->category[$searchCriteria['category']], 'a');
    
        if ($searchCriteria['category'] == Constants::SEARCH_CATEGORY_DOCTOR) {
            $this->queryBuilder->andWhere('a.firstName LIKE :seachTerm OR a.middleName LIKE :seachTerm OR a.lastName LIKE :seachTerm');
            $this->queryBuilder->setParameter('seachTerm', '%'.$searchCriteria['term'].'%');
        }
        else if ($searchCriteria['category'] == Constants::SEARCH_CATEGORY_CENTER) {
    
            $this->queryBuilder->join('a.institution', 'b');
            $this->queryBuilder->join('a.institutionSpecializations', 'c');
            $this->queryBuilder->innerJoin('c.specialization', 'd');
            $this->queryBuilder->where('a.institution = b.id');
            $this->queryBuilder->andWhere('a.id = c.institutionMedicalCenter');
            $this->queryBuilder->andWhere('c.specialization = d.id');
            $this->queryBuilder->andWhere('a.name LIKE :name');
            $this->queryBuilder->setParameter('name', '%'.$searchCriteria['term'].'%');
             
        }else{
            $this->queryBuilder->andWhere('a.name LIKE :name');
            $this->queryBuilder->setParameter('name', '%'.$searchCriteria['term'].'%');
        }
    
        $result = $this->setPager($this->queryBuilder);
        return $result->getResults();
    }
    
    function setPager($query)
    {
        $adapter = new DoctrineOrmAdapter($query);
         
        $params['page'] = isset($this->queryParams['page']) ? $this->queryParams['page'] : $this->pagerDefaultOptions['page'];
        $params['limit'] = isset($this->queryParams['limit']) ? $this->queryParams['limit'] : $this->pagerDefaultOptions['limit'];
    
        $this->pager = new Pager($adapter, $params);
    
        return $this->pager;
    }
    
    function getPager()
    {
        return $this->pager;
    }
}