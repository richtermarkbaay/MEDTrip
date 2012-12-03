<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

class CategorySearchResultBuilder extends SearchResultBuilder
{
    protected function buildQueryBuilder()
    {
        
    }
    
    protected function buildResult()
    {
        $result = new AdminSearchResult();
        $result->setName();
    }
}