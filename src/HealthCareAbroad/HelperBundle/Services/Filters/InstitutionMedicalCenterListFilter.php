<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

class InstitutionMedicalCenterListFilter extends DoctrineOrmListFilter
{
    function setFilterOptions()
    {
        $statusFilterOptions = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);

        if (isset($this->queryParams['isInstitutionContext']) && $this->queryParams['isInstitutionContext']) {
            $statusFilterOptions += InstitutionMedicalCenterStatus::getStatusListForInstitutionContext();
        } else {
            $statusFilterOptions += InstitutionMedicalCenterStatus::getStatusList();
        }

        $this->setStatusFilterOption($statusFilterOptions);
    }

    public function setFilteredResults()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('InstitutionBundle:InstitutionMedicalCenter', 'a');
        $this->queryBuilder->where('a.institution = :institutionId');
        $this->queryBuilder->setParameter('institutionId', $this->queryParams['institutionId']);

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        if(!$this->sortBy) {
            $sort = 'a.name ' . $this->sortOrder;
        } else {
            $sort = 'a.' . $this->sortBy. ' ' . $this->sortOrder;            
        }

        $this->queryBuilder->add('orderBy', $sort);
        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}