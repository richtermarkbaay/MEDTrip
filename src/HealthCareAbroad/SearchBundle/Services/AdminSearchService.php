<?php
namespace HealthCareAbroad\SearchBundle\Services;

use HealthCareAbroad\SearchBundle\Services\Admin\SearchResultBuilderFactory;

use HealthCareAbroad\SearchBundle\Classes\SearchCategoryBuilder;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\MedicalProcedureBundle\Entity\Treatment;
use HealthCareAbroad\SearchBundle\Constants;
use HealthCareAbroad\AdminBundle\Entity\SearchAdminResults;
use HealthCareAbroad\HelperBundle\Entity\Country;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

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
	
	public function setSearchBuilderFactory(SearchResultBuilderFactory $sf)
	{
		$this->factory = $sf;
	}

	public function search(array $searchCriteria = array())
	{
		$builder = $this->factory->getBuilderByCategory($searchCriteria);
		var_dump($builder);exit;
		$result = $builder->search($searchCriteria);
		
    	return $result;
	}

}