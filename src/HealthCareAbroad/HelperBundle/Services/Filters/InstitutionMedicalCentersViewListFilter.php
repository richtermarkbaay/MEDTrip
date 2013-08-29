<?php 
/**
 * @autor Chaztine Blance
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use Doctrine\DBAL\Query\QueryBuilder;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

class InstitutionMedicalCentersViewListFilter extends DoctrineOrmListFilter
{
    function __construct($doctrine)
    {
        parent::__construct($doctrine);
        $this->addValidCriteria('name');
        // Add status in validCriteria
        $this->addValidCriteria('status');
        
    }
    function setFilterOptions()
    {
        $statusFilterOptions = array(ListFilter::FILTER_KEY_ALL => ListFilter::FILTER_LABEL_ALL);

        if (isset($this->queryParams['isInstitutionContext']) && $this->queryParams['isInstitutionContext']) {
            
            $statusFilterOptions += InstitutionMedicalCenterStatus::getStatusListForInstitutionContext();
        } else {
            
            $statusFilterOptions += InstitutionMedicalCenterStatus::getStatusList();
        }

        $this->setStatusFilterOption($statusFilterOptions);
        $this->setNameFilterOption();
    }
    
    function setNameFilterOption()
    {
        if($this->queryParams['name'] == 'all') {
            
            $this->queryParams['name'] = '';
        }
    
        $this->filterOptions['name'] = array('label' => 'Medical Center Name', 'value' => isset($this->queryParams['name']) ? $this->queryParams['name'] : '' );
    }
    
    function setFilteredResults()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('InstitutionBundle:InstitutionMedicalCenter', 'a');
        
        if (trim($this->queryParams['name'])) {
            
            $this->queryBuilder->andWhere('a.name LIKE :name');
            $this->queryBuilder->setParameter('name', "%" . $this->queryParams['name'] . "%");
        }
        
        if ($this->queryParams['status'] != ListFilter::FILTER_KEY_ALL) {
            
            $this->queryBuilder->where('a.status = :status');
            $this->queryBuilder->setParameter('status', $this->queryParams['status']);
        }
        
        if($this->sortBy){
            
            $sortBy = $this->sortBy;
            $sort = "a.$sortBy " . $this->sortOrder;
        }else{
            
            $sortBy = 'dateUpdated';
            $sort = "a.$sortBy " . 'desc';
        }
        
        $this->queryBuilder->add('orderBy', $sort);
        $this->pagerAdapter->setQueryBuilder($this->queryBuilder);
        $this->pager->setLimit(50);
        
        $this->filteredResult = $this->pager->getResults();
    }
}