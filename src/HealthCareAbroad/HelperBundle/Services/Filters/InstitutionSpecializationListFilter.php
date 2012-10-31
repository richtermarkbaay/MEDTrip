<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

class InstitutionSpecializataionListFilter extends ListFilter
{
    function setFilterOptions()
    {
        $this->setStatusFilterOption();
    }
}