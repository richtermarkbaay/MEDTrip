<?php
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

class SubSpecializationListFilter extends DoctrineOrmListFilter
{
    protected $serviceDependencies = array(
        'services.treatment_bundle'
    );

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // set default status filter to active
        $this->defaultParams = array('status' => SubSpecialization::STATUS_ACTIVE);
        
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
        $treatmentBundleService = $this->getInjectedDependcy('services.treatment_bundle');
        $specializations = $treatmentBundleService->getAllActiveSpecializations();
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

    function setFilteredResults()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
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
        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}