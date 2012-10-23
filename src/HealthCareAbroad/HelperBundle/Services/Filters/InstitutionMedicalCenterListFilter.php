<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroupStatus;

class InstitutionMedicalCenterListFilter extends ListFilter
{
    function setFilterOptions()
    {
        $statusFilterOptions = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);

        if (isset($this->queryParams['isInstitutionContext']) && $this->queryParams['isInstitutionContext']) {
            $statusFilterOptions += InstitutionMedicalCenterGroupStatus::getStatusListForInstitutionContext();
        } else {
            $statusFilterOptions += InstitutionMedicalCenterGroupStatus::getStatusList();
        }

        $this->setStatusFilterOption($statusFilterOptions);
    }

    public function buildQueryBuilder()
    {
        $this->queryBuilder->select('a')->from('InstitutionBundle:InstitutionMedicalCenterGroup', 'a');
        $this->queryBuilder->where('a.institution = :institutionId');
        $this->queryBuilder->setParameter('institutionId', $this->queryParams['institutionId']);

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

//         if(!$this->sortBy || $this->sortBy == 'medicalCenter') {
//             $sort = 'b.name ' . $this->sortOrder;
//         } else {
//             $sort = 'a.' . $this->sortBy. ' ' . $this->sortOrder;            
//         }

//         $this->queryBuilder->add('orderBy', $sort);
    }
}