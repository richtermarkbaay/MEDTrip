<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

class InstitutionSpecializationListFilter extends DoctrineOrmListFilter
{
    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }

    function setFilteredResults()
    {
        $this->queryBuilder = $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('InstitutionBundle:InstitutionSpecialization', 'a');
        $this->queryBuilder->leftJoin('a.institutionMedicalCenter', 'b');
        $this->queryBuilder->where('b.institution = :institutionId');
        $this->queryBuilder->setParameter('institutionId', $this->queryParams['institutionId']);

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }


        if(!$this->sortBy || $this->sortBy === 'specialization') {
            $this->queryBuilder->leftJoin('a.specialization', 'c');
            $sort = 'c.name ' . $this->sortOrder;
        } else {
            $sort = 'a.' . $this->sortBy. ' ' . $this->sortOrder;
        }

        $this->queryBuilder->add('orderBy', $sort);
        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}