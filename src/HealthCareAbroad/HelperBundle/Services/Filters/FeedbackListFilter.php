<?php 
/**
 * @autor Chaztine Blance
 * 
 * @author Adelbert Silla - Revised implementation and fixed bugs (date: 12-12-2013)
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Symfony\Component\Validator\Constraints\Date;

class FeedbackListFilter extends DoctrineOrmListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
        $this->addValidCriteria('keyword');
        $this->addValidCriteria('country');
        $this->addValidCriteria('dateCreated');

        $this->defaultParams['keyword'] = '';
        $this->defaultParams['dateCreated'] = '';
        
        $this->pagerDefaultOptions['limit'] = '10000';

        //manually inject service for serviceDependencies
        $this->serviceDependencies = array('services.location');
    }
    function setFilterOptions()
    {
        $this->setKeywordFilterOption();
        $this->setCountryFilterOption();
        $this->setDateCreatedFilterOption();
    }

    function setKeywordFilterOption()
    {
        $this->filterOptions['keyword'] = array(
            'label' => 'Search for keyword',
            'value' => $this->queryParams['keyword'],
        );
    }

    function setCountryFilterOption()
    {
        $options = array(self::FILTER_KEY_ALL => self::FILTER_LABEL_ALL);
    
        // Set The Filter Option
        $countries = $this->getInjectedDependcy('services.location')->getGlobalCountries();
        foreach($countries['data'] as $each) {
            $options[$each['id']] = $each['name'];
        }
    
        $this->filterOptions['country'] = array(
            'label' => 'Country',
            'selected' => $this->queryParams['country'],
            'options' => $options
        );
    }

    function setDateCreatedFilterOption()
    {    
        $this->filterOptions['dateCreated'] = array(
            'label' => 'Date',
            'value' => $this->queryParams['dateCreated']
        );
    }
    
    function setFilteredResults()
    {
        $this->queryBuilder = $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a, b')->from('HelperBundle:FeedbackMessage', 'a')->leftJoin('a.country', 'b');

        if ($this->queryParams['keyword']) {
            $this->queryBuilder->where('a.name LIKE :searchKey OR a.emailAddress LIKE :searchKey OR a.message LIKE :searchKey');
            $this->queryBuilder->setParameter('searchKey', '%'.$this->queryParams['keyword'].'%' );
        }
        
        if ($this->queryParams['country'] != self::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }

        if ($this->queryParams['dateCreated']) {
            list($year, $month, $day) = explode('-', $this->queryParams['dateCreated']);

            $dateFrom = new \DateTime();
            $dateFrom->setDate($year, $month, $day);
            $dateFrom->setTime('00', '00', '00');

            $dateTo = clone $dateFrom;
            $dateTo->setTime('23', '59', '59');

            $this->queryBuilder->andWhere('a.dateCreated >= :dateFrom AND a.dateCreated <= :dateTo');
            $this->queryBuilder->setParameter('dateFrom', $dateFrom)->setParameter('dateTo', $dateTo);
        }

        $sortBy = $this->sortBy ? $this->sortBy : 'dateCreated';
        $sort = "a.$sortBy " . $this->sortOrder;

        $this->queryBuilder->add('orderBy', $sort);        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        //echo $this->queryBuilder->getQuery()->getSQL(); exit;

        $this->filteredResult = $this->pager->getResults();
    }
}