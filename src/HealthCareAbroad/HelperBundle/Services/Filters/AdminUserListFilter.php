<?php 
/**
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\HelperBundle\Services\Filters;
use HealthCareAbroad\UserBundle\Entity\SiteUser;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

class AdminUserListFilter extends ListFilter
{

    function __construct($doctrine)
    {
        parent::__construct($doctrine);

        // Add advertisementType in validCriteria
        // $this->addValidCriteria('label');
    }

    function setFilterOptions()
    {
        //$this->setAdvertisementTypeFilterOption();

        //$this->setStatusFilterOption();
        
    }

    function buildQueryBuilder()
    {
        $this->queryBuilder->select('a, b')->from('UserBundle:AdminUser', 'a');
        $this->queryBuilder->leftJoin('a.adminUserType', 'b');
        $this->queryBuilder->where('a.status != :status');
        $this->queryBuilder->setParameter('status', SiteUser::STATUS_INACTIVE);

        $sortBy = $this->sortBy ? $this->sortBy : 'status';
        $sort = "a.$sortBy " . $this->sortOrder;

    	$this->queryBuilder->add('orderBy', $sort);
    	
    }
}