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

        // Add medicalProcedureType in validCriteria
        $this->addValidCriteria('medicalProcedureType');
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

        $this->filterOptions['medicalProcedureType'] = array(
            'label' => 'Procedure Type',
            'selected' => $this->queryParams['medicalProcedureType'],
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
        
        if ($this->queryParams['medicalProcedureType'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.medicalProcedureType = :medicalProcedureType');
            $this->queryBuilder->setParameter('medicalProcedureType', $this->criteria['medicalProcedureType']);
        }

        if($this->sortBy == 'medicalProcedureType') {
            $this->queryBuilder->leftJoin('a.medicalProcedureType', 'b');
            $sort = 'b.name ' . $this->sortOrder;
        } else {
            $sortBy = $this->sortBy ? $this->sortBy : 'name';
            $sort = "a.$sortBy " . $this->sortOrder;          
        }

        $this->queryBuilder->add('orderBy', $sort);
    }
}