<?php 
/**
 * @autor Alnie Jacobe
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Symfony\Component\Validator\Constraints\Date;

use HealthCareAbroad\AdminBundle\Entity\InquirySubject;

class InstitutionContactDetailListFilter extends DoctrineOrmListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
        $this->addValidCriteria('country');
    }
    
    function setFilterOptions()
    {
        $this->setCountryFilterOption();
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
    
    function setFilteredResults()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a, b, c')->from('InstitutionBundle:Institution', 'a');
        $this->queryBuilder->leftJoin('a.institutionUsers', 'b');
        $this->queryBuilder->leftJoin('a.contactDetails', 'c');

        if ($this->queryParams['country'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.country = :country');
            $this->queryBuilder->setParameter('country', $this->queryParams['country']);
        }
        
        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "a.$sortBy " . $this->sortOrder;
        
        $this->queryBuilder->add('orderBy', $sort);

        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);

        $this->filteredResult = $this->pager->getResults();
    }
}