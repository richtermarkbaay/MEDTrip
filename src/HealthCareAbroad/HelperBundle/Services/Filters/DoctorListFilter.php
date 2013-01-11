<?php 
/**
 * @autor Alnie Jacobe
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\DBAL\Query\QueryBuilder;

class DoctorListFilter extends ListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    
        // Add country in validCriteria
        $this->addValidCriteria('country');
    }
    function setFilterOptions()
    {
        $this->setCountryFilterOption();
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
    
    function buildQueryBuilder()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('DoctorBundle:Doctor', 'a');

        if ($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }
        
        $sortBy = $this->sortBy ? $this->sortBy : 'firstName';
        $sort = "a.$sortBy " . $this->sortOrder;

        $this->queryBuilder->add('orderBy', $sort);
    }
}