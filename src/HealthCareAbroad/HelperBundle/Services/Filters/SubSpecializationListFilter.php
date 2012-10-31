<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

class SubSpecializationListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add specialization in validCriteria
        $this->addValidCriteria('specialization');
    }

    function setFilterOptions()
    {
        $this->setSpecializationFilterOption();

        $this->setStatusFilterOption();
    }

    function setSpecializationFilterOption()
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
        $this->queryBuilder->select('a')->from('TreatmentBundle:SubSpecialization', 'a');

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