<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Doctrine\DBAL\Query\QueryBuilder;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;


class InstitutionListFilter extends ListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    
        // Add country in validCriteria
        $this->addValidCriteria('country');
        $this->addValidCriteria('type');
    }
    function setFilterOptions()
    {
        $statusOptions = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL) + InstitutionStatus::getBitValueLabels();
        $this->setCountryFilterOption();
        $this->setTypeFilterOption();
        $this->setStatusFilterOption($statusOptions);
    }
    
    function setCountryFilterOption()
    {
        $countries = $this->doctrine->getEntityManager()->getRepository('HelperBundle:Country')->findAll();
        
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($countries as $each) {
            $options[$each->getId()] = $each->getName();
        }

        $this->filterOptions['country'] = array(
            'label' => 'Country',
            'selected' => $this->queryParams['country'],
            'options' => $options
        );
    }
    
    function setTypeFilterOption()
    {
        $typeLabel = InstitutionTypes::getLabelList();
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        $options[InstitutionTypes::MULTIPLE_CENTER] = $typeLabel[InstitutionTypes::MULTIPLE_CENTER];
        $options[InstitutionTypes::MEDICAL_TOURISM_FACILITATOR] = $typeLabel[InstitutionTypes::MEDICAL_TOURISM_FACILITATOR];
        $options[InstitutionTypes::SINGLE_CENTER] = $typeLabel[InstitutionTypes::SINGLE_CENTER];

        $this->filterOptions['type'] = array(
            'label' => 'Institution Types',
            'selected' => $this->queryParams['type'],
            'options' => $options
        );
          
    }
    
    function buildQueryBuilder()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('InstitutionBundle:Institution', 'a');
        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
        
        if ($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }
        
        if ($this->queryParams['type'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.type = :type');
            $this->queryBuilder->setParameter('type', $this->queryParams['type']);
        }
        
        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "a.$sortBy " . $this->sortOrder;

        $this->queryBuilder->add('orderBy', $sort);
    }
}