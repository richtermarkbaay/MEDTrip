<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

class InstitutionSpecializationListFilter extends ListFilter
{
    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }
    
    function buildQueryBuilder()
    {
        return;
    }
}