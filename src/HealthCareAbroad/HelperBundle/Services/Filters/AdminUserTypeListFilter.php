<?php 
/**
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\HelperBundle\Services\Filters;
use HealthCareAbroad\UserBundle\Entity\AdminUserType;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

class AdminUserTypeListFilter extends DoctrineOrmListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add advertisementType in validCriteria
        $this->addValidCriteria('name');
    }

    function setFilterOptions()
    {
        //$this->setAdvertisementTypeFilterOption();

        //$this->setStatusFilterOption();
        
    }

    function setFilteredResults()
    {
        $this->queryBuilder =  $this->doctrine->getEntityManager()->createQueryBuilder();
        $this->queryBuilder->select('a')->from('UserBundle:AdminUserType', 'a');
        $this->queryBuilder->where('a.status = :active OR a.status = :inactive');
        $this->queryBuilder->setParameter('active', AdminUserType::STATUS_ACTIVE);
        $this->queryBuilder->setParameter('inactive', AdminUserType::STATUS_INACTIVE);

        $sortBy = $this->sortBy ? $this->sortBy : 'name';
        $sort = "a.$sortBy " . $this->sortOrder;

    	$this->queryBuilder->add('orderBy', $sort);
    	
    	$this->pagerAdapter->setQueryBuilder($this->queryBuilder);
    	
    	$this->filteredResult = $this->pager->getResults();
    }
}