<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class MedicalProcedureListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add treatment in validCriteria
        $this->addValidCriteria('treatment');
    }

    function setFilterOptions()
    {
        $this->setMedicalProcedureTypeFilterOption();

        $this->setStatusFilterOption();
    }

    function setMedicalProcedureTypeFilterOption()
    {        
        // Set The Filter Option 
        $procedureTypes = $this->doctrine->getEntityManager()->getRepository('MedicalProcedureBundle:Treatment')->findByStatus(1);
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($procedureTypes as $each) {
            $options[$each->getId()] = $each->getName();
        }

        $this->filterOptions['treatment'] = array(
            'label' => 'Procedure Type',
            'selected' => $this->queryParams['treatment'],
            'options' => $options
        );
    }

    function buildQueryBuilder()
    {
        $this->queryBuilder->select('a')->from('MedicalProcedureBundle:TreatmentProcedure', 'a');

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
        
        if ($this->queryParams['treatment'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.treatment = :treatment');
            $this->queryBuilder->setParameter('treatment', $this->criteria['treatment']);
        }

        if($this->sortBy == 'treatment') {
            $this->queryBuilder->leftJoin('a.treatment', 'b');
            $sort = 'b.name ' . $this->sortOrder;
        } else {
            $sortBy = $this->sortBy ? $this->sortBy : 'name';
            $sort = "a.$sortBy " . $this->sortOrder;          
        }

        $this->queryBuilder->add('orderBy', $sort);
    }
}