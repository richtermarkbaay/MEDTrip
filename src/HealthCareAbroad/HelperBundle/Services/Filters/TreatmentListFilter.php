<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

class TreatmentListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add treatment in validCriteria
        $this->addValidCriteria('subSpecialization');
    }

    function setFilterOptions()
    {
        $this->setMedicalProcedureTypeFilterOption();

        $this->setStatusFilterOption();
    }

    function setMedicalProcedureTypeFilterOption()
    {        
        // Set The Filter Option 
        $subSpecializations = $this->doctrine->getEntityManager()->getRepository('TreatmentBundle:SubSpecialization')->findByStatus(Treatment::STATUS_ACTIVE);
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($subSpecializations as $each) {
            $options[$each->getId()] = $each->getName();
        }

        $this->filterOptions['treatment'] = array(
            'label' => 'Sub-specialization',
            'selected' => $this->queryParams['subSpecialization'],
            'options' => $options
        );
    }

    function buildQueryBuilder()
    {
        $this->queryBuilder->select('a')->from('TreatmentBundle:Treatment', 'a');

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
        
        if ($this->queryParams['subSpecialization'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.subSpecialization = :subSpecialization');
            $this->queryBuilder->setParameter('subSpecialization', $this->criteria['subSpecialization']);
        }

        if($this->sortBy == 'subSpecialization') {
            $this->queryBuilder->leftJoin('a.subSpecialization', 'b');
            $sort = 'b.name ' . $this->sortOrder;
        } else {
            $sortBy = $this->sortBy ? $this->sortBy : 'name';
            $sort = "a.$sortBy " . $this->sortOrder;          
        }

        $this->queryBuilder->add('orderBy', $sort);
    }
}