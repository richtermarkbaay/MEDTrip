<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

use HealthCareAbroad\SearchBundle\Constants;

class SearchResultBuilderFactory
{
    static private $builderMapping = array();
    
    
    /**
     * 
     * @param unknown_type $category
     * @return SearchResultBuilder
     */
    static public function getBuilderByCategory($category)
    {
        
        $cls =  static::$builderMapping[$category];
        return new $cls;
    }
    
    static public function _initMapping()
    {
        static::$builderMapping = array(
                    Constants::SEARCH_CATEGORY_DOCTOR => 'HealthCareAbroad\SearchBundle\Services\Admin\DoctorSearchResultBuilder'
                    );
    }
}

SearchResultBuilderFactory::_initMapping();