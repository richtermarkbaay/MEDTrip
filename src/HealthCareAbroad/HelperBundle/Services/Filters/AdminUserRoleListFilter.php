<?php 
/**
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\HelperBundle\Services\Filters;
use HealthCareAbroad\UserBundle\Entity\AdminUserRole;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

class AdminUserRoleListFilter extends DoctrineOrmListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add advertisementType in validCriteria
        $this->addValidCriteria('label');
    }

    function setFilterOptions()
    {
        //$this->setAdvertisementTypeFilterOption();

        //$this->setStatusFilterOption();
        
    }

    function setFilteredResults()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('UserBundle:AdminUserRole', 'a');
        $this->queryBuilder->where('a.status = :status');
        $this->queryBuilder->setParameter('status', AdminUserRole::STATUS_ACTIVE);

        $sortBy = $this->sortBy ? $this->sortBy : 'label';
        $sort = "a.$sortBy " . $this->sortOrder;

    	$this->queryBuilder->add('orderBy', $sort);
    	
    	$this->pagerAdapter->setQueryBuilder($this->queryBuilder);
    	
    	$this->filteredResult = $this->pager->getResults();
    }
}