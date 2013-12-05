<?php 

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\HelperBundle\Entity\City;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

class InstitutionMedicalCenterRankingListFilter extends DoctrineOrmListFilter
{
    protected $defaultParams = array(
        'name' => ''
    );
    
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    
        // Add country in validCriteria
        $this->addValidCriteria('country');
        $this->addValidCriteria('city');
        $this->addValidCriteria('name');
    
    }
    function setFilterOptions()
    {
        $this->setNameFilter();
        $this->setCountryFilterOption();
        $this->setCityFilterOption();
    }
    
    public function setNameFilter()
    {
        $this->filterOptions['name'] = array(
            'label' => 'Name',
            'value' => '',
            'placeholder' => 'Search by institution'
        );
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
    
    function setFilteredResults()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder
            ->select('a, inst')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
            ->innerJoin('a.institution', 'inst');
        
        $this->queryBuilder->andWhere('inst.status = :approved_status')->setParameter('approved_status', InstitutionStatus::getBitValueForApprovedStatus());
        
        $name = \trim($this->queryParams['name']);
        if ('' != $name){
            $this->queryBuilder->andWhere('inst.name LIKE :name')
            ->setParameter('name', "%{$name}%");
            
        }
        
        if ($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
            
            $this->queryBuilder->andWhere('inst.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }
        
        if ($this->queryParams['city'] != ListFilter::FILTER_KEY_ALL) {
            
            $this->queryBuilder->andWhere('inst.city = :city');
            $this->queryBuilder->setParameter('city', $this->queryParams['city']);
        }
        
        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "a.$sortBy " . $this->sortOrder;

        $this->queryBuilder->add('orderBy', $sort);
        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}