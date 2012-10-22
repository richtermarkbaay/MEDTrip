<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

class TreatmentListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);
        
        // Add specialization in validCriteria
        $this->addValidCriteria('specialization');
    }

    function setFilterOptions()
    {
        $this->setMedicalCenterFilterOption();    

        $this->setStatusFilterOption();
    }

    function setMedicalCenterFilterOption()
    {        

        // Set The Filter Option 
        $medicalCenters = $this->doctrine->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalCenter')->findByStatus(MedicalCenter::STATUS_ACTIVE);
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($medicalCenters as $each) {
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
        $this->queryBuilder->select('a')->from('MedicalProcedureBundle:Treatment', 'a');
    
        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
         
        if ($this->queryParams['specialization'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.medicalCenter = :specialization');
            $this->queryBuilder->setParameter('specialization', $this->criteria['specialization']);
        }

        if($this->sortBy == 'specialization') {
            $this->queryBuilder->leftJoin('a.medicalCenter', 'b');
            $sort = 'b.name ' . $this->sortOrder;

        } else {
            $sortBy = $this->sortBy ? $this->sortBy : 'name';
            $sort = "a.$sortBy " . $this->sortOrder;
        }

        $this->queryBuilder->add('orderBy', $sort);
    }
}