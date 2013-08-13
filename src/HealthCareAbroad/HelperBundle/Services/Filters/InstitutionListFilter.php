<?php 
/**
 * @autor Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Doctrine\DBAL\Query\QueryBuilder;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;


class InstitutionListFilter extends DoctrineOrmListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    
        // Add country in validCriteria
        $this->addValidCriteria('country');
        $this->addValidCriteria('type');
        $this->addValidCriteria('payingClient');
        $this->addValidCriteria('name');
    }
    function setFilterOptions()
    {
        $statusOptions = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL) + InstitutionStatus::getBitValueLabels();
        
        $this->setNameFilterOption();
        $this->setPayingClientOption();
        $this->setCountryFilterOption();
        $this->setTypeFilterOption();
        $this->setStatusFilterOption($statusOptions);
    }
    
    function setNameFilterOption()
    {
        if($this->queryParams['name'] == 'all') {
            $this->queryParams['name'] = '';
        }
        
        $this->filterOptions['name'] = array('label' => 'Institution Name', 'value' => isset($this->queryParams['name']) ? $this->queryParams['name'] : '' );
    }
    
    function setCountryFilterOption()
    {
        $countries = $this->doctrine->getEntityManager()->getRepository('HelperBundle:Country')->findBy(array('status' => Country::STATUS_ACTIVE),array('name' => 'ASC'));
        
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
        //$options[InstitutionTypes::MEDICAL_TOURISM_FACILITATOR] = $typeLabel[InstitutionTypes::MEDICAL_TOURISM_FACILITATOR];
        $options[InstitutionTypes::SINGLE_CENTER] = $typeLabel[InstitutionTypes::SINGLE_CENTER];

        $this->filterOptions['type'] = array(
            'label' => 'Institution Types',
            'selected' => $this->queryParams['type'],
            'options' => $options
        );
    }
    
    function setPayingClientOption()
    {
        $options = array(1 => 'Yes', 0 => 'No', ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);

        $this->filterOptions['payingClient'] = array(
            'label' => 'Paying Client',
            'selected' => $this->queryParams['payingClient'],
            'options' => $options
        );
    }
    
    function setFilteredResults()
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
        
        if ($this->queryParams['payingClient'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.payingClient = :payingClient');
            $this->queryBuilder->setParameter('payingClient', $this->queryParams['payingClient']);
        }
        
        if (trim($this->queryParams['name'])) {
            $this->queryBuilder->andWhere('a.name LIKE :name');
            $this->queryBuilder->setParameter('name', "%" . $this->queryParams['name'] . "%");
        }
        
        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "a.$sortBy " . $this->sortOrder;

        $this->queryBuilder->add('orderBy', $sort);
        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->pager->setLimit(50);
        
        $this->filteredResult = $this->pager->getResults();
    }
}