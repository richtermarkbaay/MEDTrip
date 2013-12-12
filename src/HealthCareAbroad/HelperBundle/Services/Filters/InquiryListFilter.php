<?php 
/**
 * @autor Chaztine Blance
 * 
 * @author Adelbert Silla - Revised implementation and fixed bugs (date: 12-12-2013)
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\ORM\Query;

use Symfony\Component\Validator\Constraints\Date;

use HealthCareAbroad\AdminBundle\Entity\InquirySubject;

class InquiryListFilter extends DoctrineOrmListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
        $this->addValidCriteria('keyword');
        $this->addValidCriteria('type');
        $this->addValidCriteria('country');
        $this->addValidCriteria('dateCreated');
        
        $this->defaultParams['keyword'] = '';
        $this->defaultParams['dateCreated'] = '';

        $this->pagerDefaultOptions['limit'] = '100000';

        //manually inject service for serviceDependencies
        $this->serviceDependencies = array('services.location');
    }

    function setFilterOptions()
    {
        $this->setKeywordFilterOption();
        $this->setInqueryTypeFilterOption();
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

    function setInqueryTypeFilterOption()
    {
        $options = array(self::FILTER_KEY_ALL => self::FILTER_LABEL_ALL);

        $qb = $this->doctrine->getEntityManagerForClass('AdminBundle:InquirySubject')->createQueryBuilder();
        $qb->select('a.id, a.name')->from('AdminBundle:InquirySubject', 'a')->where('a.status = :status')->setParameter('status', 1);
        
        $inquiryTypes = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        
        foreach($inquiryTypes as $each) {
            $options[$each['id']] = $each['name'];
        }

        $this->filterOptions['type'] = array(
            'label' => 'Inquiry Type',
            'options' => $options,
            'selected' => $this->queryParams['type'],
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
        $this->queryBuilder->select('a, b')->from('AdminBundle:Inquiry', 'a')->leftJoin('a.country', 'b');

        if ($this->queryParams['keyword']) {
            $this->queryBuilder->where('a.lastName LIKE :searchKey OR a.firstName LIKE :searchKey OR a.email LIKE :searchKey OR a.message LIKE :searchKey OR a.contactNumber LIKE :searchKey');
            $this->queryBuilder->setParameter('searchKey', '%'.$this->queryParams['keyword'].'%' );
        }

        if ($this->queryParams['type'] != self::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.inquirySubject = :subject');
            $this->queryBuilder->setParameter('subject', $this->queryParams['type']);
        }

        if ($this->queryParams['country'] != self::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }

        if ($this->queryParams['dateCreated']) {
            list($year, $month, $day) =explode('-', $this->queryParams['dateCreated']);

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

        $this->filteredResult = $this->pager->getResults();
    }
}