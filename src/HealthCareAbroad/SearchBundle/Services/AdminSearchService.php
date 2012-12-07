<?php
namespace HealthCareAbroad\SearchBundle\Services;

use HealthCareAbroad\SearchBundle\Services\Admin\SearchAdminPagerService;
use HealthCareAbroad\SearchBundle\Services\Admin\SearchResultBuilderFactory;
use HealthCareAbroad\SearchBundle\Classes\SearchCategoryBuilder;
use Symfony\Component\HttpFoundation\Request;
use HealthCareAbroad\AdminBundle\Entity\SearchAdminResults;

/**
 * Temporary holder of all search related functionality
 *
 */
class AdminSearchService
{
	/**
	 * @var SearchResultBuilderFactory
	 */
	private $factory;
	
	public function setSearchBuilderFactory(SearchResultBuilderFactory $searchfactory)
	{
		$this->factory = $searchfactory;
	}

	public function search(array $searchCriteria = array(), SearchAdminPagerService $p)
	{
		$builder = $this->factory->getBuilderByCategory($searchCriteria);
		$result = $builder->search($searchCriteria, $p);
		
		return $result;
	}
}