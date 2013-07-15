<?php 

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Entity\City;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

class InstitutionRankingListFilter extends ListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    
        // Add country in validCriteria
        $this->addValidCriteria('country');
        $this->addValidCriteria('city');
    
    }
    function setFilterOptions()
    {
        $this->setCountryFilterOption();
        $this->setCityFilterOption();
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
    
    function setCityFilterOption()
    {
        $cityRepo = $this->doctrine->getEntityManager()->getRepository('HelperBundle:City');
        if($this->queryParams['country'] == ListFilter::FILTER_KEY_ALL) {
            $cities = $cityRepo->findBy(array('status' => City::STATUS_ACTIVE),array('name' => 'ASC'));
        }
        else {
            $cities = $cityRepo->findBy(array('country' => $this->queryParams['country']), array('name' => 'ASC'));
        }
    
        $options = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);
        foreach($cities as $each) {
            $options[$each->getId()] = $each->getName();
        }
    
        $this->filterOptions['city'] = array(
                        'label' => 'City',
                        'selected' => $this->queryParams['city'],
                        'options' => $options
        );
    }
    
    function buildQueryBuilder()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('InstitutionBundle:Institution', 'a');
        $this->queryBuilder->andWhere('a.status = :approved_status')->setParameter('approved_status', InstitutionStatus::getBitValueForApprovedStatus());
        if ($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }
        
        if ($this->queryParams['city'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.city = :city');
            $this->queryBuilder->setParameter('city', $this->queryParams['city']);
        }
        
        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "a.$sortBy " . $this->sortOrder;

        $this->queryBuilder->add('orderBy', $sort);
    }
}