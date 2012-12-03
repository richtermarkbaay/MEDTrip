<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\SearchBundle\Constants;

class SearchResultBuilderFactory
{
    static private $builderMapping = array();
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function __construct(Registry $doctrine)
    {
    	$this->doctrine = $doctrine;
    }
    
    
    /**
     * 
     * @param unknown_type $category
     * @return SearchResultBuilder
     */
    public function getBuilderByCategory($category)
    {
        $cls =  static::$builderMapping[$category['category']];
        return new $cls($this->doctrine);
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