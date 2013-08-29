<?php
/**
 * @autor Chaztine Blance
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\ORM\QueryBuilder;

class AwardingBodyListFilter extends DoctrineOrmListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
    
        $this->addValidCriteria('name');
    }
    
    function setFilterOptions()
    {
        $this->setStatusFilterOption();
        $this->setNameFilterOption();
    }
    
    function setNameFilterOption()
    {
        if($this->queryParams['name'] == 'all') {
            $this->queryParams['name'] = '';
        }
    
        $this->filterOptions['name'] = array('label' => 'Awarding Body', 'value' => isset($this->queryParams['name']) ? $this->queryParams['name'] : '' );
    }
    
    function setFilteredResults()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('c')->from('HelperBundle:AwardingBody', 'c');

        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            $this->queryBuilder->where('c.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
        
        if (trim($this->queryParams['name'])) {
            $this->queryBuilder->andWhere('c.name LIKE :name');
            $this->queryBuilder->setParameter('name', "%" . $this->queryParams['name'] . "%");
        }
        
        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "c.$sortBy " . $this->sortOrder;
        
        $this->queryBuilder->add('orderBy', $sort);
        
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        
        $this->filteredResult = $this->pager->getResults();
    }
}