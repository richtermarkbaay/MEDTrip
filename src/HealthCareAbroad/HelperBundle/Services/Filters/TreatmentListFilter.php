<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

class TreatmentListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);
        
        // set default status filter to active
        $this->defaultParams = array('status' => Treatment::STATUS_ACTIVE);

        // Add treatment in validCriteria
        $this->addValidCriteria('specialization');
    }

    function setFilterOptions()
    {
        $this->setMedicalProcedureTypeFilterOption();

        $this->setStatusFilterOption();
    }

    function setMedicalProcedureTypeFilterOption()
    {        
        // Set The Filter Option 
        $specializations = $this->doctrine->getEntityManager()->getRepository('TreatmentBundle:Specialization')->findByStatus(Specialization::STATUS_ACTIVE);
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($specializations as $each) {
            $options[$each->getId()] = $each->getName();
        }

        $this->filterOptions['specialization'] = array(
            'label' => 'Specialization',
            'selected' => $this->queryParams['specialization'],
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
        
        if ($this->queryParams['specialization'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.specialization = :specialization');
            $this->queryBuilder->setParameter('specialization', $this->criteria['specialization']);
        }

        if($this->sortBy == 'specialization') {
            $this->queryBuilder->leftJoin('a.specialization', 'b');
            $sort = 'b.name ' . $this->sortOrder;
        } else {
            $sortBy = $this->sortBy ? $this->sortBy : 'name';
            $sort = "a.$sortBy " . $this->sortOrder;          
        }

        $this->queryBuilder->add('orderBy', $sort);
    }
}