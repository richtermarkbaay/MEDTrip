<?php 
/**
 * @autor Chaztine Blance
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Symfony\Component\Validator\Constraints\Date;

use HealthCareAbroad\AdminBundle\Entity\InquirySubject;

class InstitutionInquiryListFilter extends DoctrineOrmListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    $this->addValidCriteria('field');
    $this->addValidCriteria('dateCreated');
    
    }
    function setFilterOptions()
    {
        $this->setInquiryFilterOption();
        $this->setDateCreatedFilterOption();
    }
    
    function setInquiryFilterOption()
    {
        $this->filterOptions['field'] = array(
                        'label' => 'Search for keyword',
                        'value' => '',
        );
    }
    function setDateCreatedFilterOption()
    {
        if($this->queryParams['dateCreated'] == 'all' ){
            $dateOptions = date("m/d/Y");
        }else{
           $dateOptions = date("m/d/Y", $this->queryParams['dateCreated']);
        }
            
        $this->filterOptions['dateCreated'] = array(
                        'label' => 'Date Created',
                        'value' => $dateOptions
        );
    }
    
    function setFilteredResults()
    {
        $this->queryBuilder = $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('InstitutionBundle:InstitutionInquiry', 'a');
        
        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
        
        if ($this->queryParams['field'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->innerJoin('a.institution', 'b'); 
            $this->queryBuilder->where('b.name LIKE :searchKey');
            $this->queryBuilder->setParameter('searchKey', '%'.$this->queryParams['field'].'%' );
        }
        
        if ($this->queryParams['dateCreated'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->andWhere('a.dateCreated >= :dateCreated');
            $this->queryBuilder->setParameter('dateCreated', date("Y-m-d H:i:s", $this->queryParams['dateCreated']) );
        }
        
    	$sortBy = 'inquirer_name  ';
    	$sort = "a.$sortBy " . $this->sortOrder;

        $this->queryBuilder->add('orderBy', $sort);
        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}