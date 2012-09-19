<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

class MedicalProcedureTypeListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);
        
        // Add medicalCenter in validCriteria
        $this->addValidCriteria('medicalCenter');
    }

    function setFilterOptions()
    {
        $this->setMedicalCenterFilterOption();    

        $this->setStatusFilterOption();
    }

    function setMedicalCenterFilterOption()
    {        

        // Set The Filter Option 
        $medicalCenters = $this->doctrine->getEntityManager()->getRepository('MedicalProcedureBundle:MedicalCenter')->findByStatus(1);
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($medicalCenters as $each) {
            $options[$each->getId()] = $each->getName();
        }

        $this->filterOptions['medicalCenter'] = array(
            'label' => 'Medical Center',
            'selected' => $this->queryParams['medicalCenter'],
            'options' => $options
        );
    }

    function buildQueryBuilder()
    {
        $this->queryBuilder->select('a')->from('MedicalProcedureBundle:MedicalProcedureType', 'a');
    
        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
         
        if ($this->queryParams['medicalCenter'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.medicalCenter = :medicalCenter');
            $this->queryBuilder->setParameter('medicalCenter', $this->criteria['medicalCenter']);
        }

        $this->queryBuilder->add('orderBy', 'a.name ASC');
    }
}