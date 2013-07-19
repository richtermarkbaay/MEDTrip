<?php 
/**
 * @autor Chaztine Blance
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Symfony\Component\Validator\Constraints\Date;

class FeedbackListFilter extends DoctrineOrmListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    $this->addValidCriteria('dateCreated');
    
    }
    function setFilterOptions()
    {
        $this->setDateCreatedFilterOption();
    }
    
    function setDateCreatedFilterOption()
    {
        $dateOptions = date("m/d/y");
    
        $this->filterOptions['dateCreated'] = array(
                        'label' => 'Date Created',
                        'value' => $dateOptions
        );
    }
    
    function setFilteredResilts()
    {
        $this->queryBuilder = $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('HelperBundle:Feedback', 'a');
    
        if ($this->queryParams['dateCreated'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('a.dateCreated >= :dateCreated');
            $this->queryBuilder->setParameter('dateCreated', date("Y-m-d", strtotime($this->queryParams['dateCreated'])) );
        }
        if($this->sortBy == 'subject') {
        		$sortBy = $this->sortBy ? $this->sortBy : 'subject';
        	$sort = "a.$sortBy " . $this->sortOrder;
        } else {
        	$sortBy = $this->sortBy ? $this->sortBy : 'dateCreated';
        	$sort = "a.$sortBy " . $this->sortOrder;
        }            

        $this->queryBuilder->add('orderBy', $sort);
        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}