<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

class InstitutionMedicalCenterListFilter extends ListFilter
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

    public function buildQueryBuilder()
    {
        $this->queryBuilder->select('a')->from('InstitutionBundle:InstitutionMedicalCenter', 'a');
        $this->queryBuilder->where('a.institution = :institutionId');
        $this->queryBuilder->leftJoin('a.medicalCenter', 'b');
        $this->queryBuilder->setParameter('institutionId', $this->queryParams['institutionId']);

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        $this->queryBuilder->add('orderBy', 'b.name ASC');
    }
}