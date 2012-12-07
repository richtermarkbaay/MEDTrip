<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

use Symfony\Component\Routing\RouteCollection;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\SearchBundle\Constants;

class SearchResultBuilderFactory
{
    static private $builderMapping = array();
    
    /**
     * @var Registry
     */
    private $doctrine;
    /**
     * @var Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;
    public function __construct(Registry $doctrine)
    {
    	$this->doctrine = $doctrine;
    }
    
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }
    
    /**
     * 
     * @param unknown_type $category
     * @return SearchResultBuilder
     */
    public function getBuilderByCategory($category)
    {
        $cls =  static::$builderMapping[$category['category']];
        $builder = new $cls($this->doctrine);
        $builder->setRouter($this->router);
        
        return $builder;
    }
    
    static public function _initMapping()
    {
        static::$builderMapping = array(
                    Constants::SEARCH_CATEGORY_INSTITUTION => 'HealthCareAbroad\SearchBundle\Services\Admin\InstitutionSearchResultBuilder',
                    Constants::SEARCH_CATEGORY_CENTER => 'HealthCareAbroad\SearchBundle\Services\Admin\MedicalCenterSearchResultBuilder',
                    Constants::SEARCH_CATEGORY_PROCEDURE_TYPE => 'HealthCareAbroad\SearchBundle\Services\Admin\TreatmentsSearchResultBuilder' ,
                    Constants::SEARCH_CATEGORY_DOCTOR => 'HealthCareAbroad\SearchBundle\Services\Admin\DoctorSearchResultBuilder',
                    Constants::SEARCH_CATEGORY_SPECIALIZATION => 'HealthCareAbroad\SearchBundle\Services\Admin\SpecializationSearchResultBuilder',
                    Constants::SEARCH_CATEGORY_SUB_SPECIALIZATION => 'HealthCareAbroad\SearchBundle\Services\Admin\SubSpecializationSearchResultBuilder'
              );
    }
}

SearchResultBuilderFactory::_initMapping();
