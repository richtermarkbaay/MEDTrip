<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\ORM\Query\Expr\Join;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

class TreatmentListFilter extends DoctrineOrmListFilter
{
    protected $serviceDependencies = array(
        'services.treatment_bundle'
    );

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
        $this->setSpecializationFilterOption();
        
        $this->setSubSpecializationFilterOption();

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
    
    public function setSubSpecializationFilterOption()
    {
        $treatmentBundleService = $this->getInjectedDependcy('services.treatment_bundle');
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        $this->filterOptions['subSpecialization'] = array(
            'label' => 'Sub Specialization',
            'selected' => ListFilter::FILTER_KEY_ALL,
            'options' => $options
        );
    }

    function setFilteredResults()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a, b')->from('TreatmentBundle:Treatment', 'a');
        
        if ($this->queryParams['specialization'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->innerJoin('a.specialization', 'b', Join::WITH, 'b.id = :specialization');
            $this->queryBuilder->setParameter('specialization', $this->criteria['specialization']);
        }
        else {
            $this->queryBuilder->leftJoin('a.specialization', 'b');
        }
        
        if (\array_key_exists('subSpecialization', $this->queryParams) &&  0 != $this->queryParams['subSpecialization']) {
            $this->queryBuilder->innerJoin('a.subSpecializations', 'c', Join::WITH, 'c.id = :subSpecialization')
            ->setParameter('subSpecialization', $this->queryParams['subSpecialization']);
        }
        else {
            $this->queryBuilder->leftJoin('a.subSpecializations', 'c');
        }
        
        $this->queryBuilder->groupBy('a.id');

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }

        if($this->sortBy == 'specialization') {
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