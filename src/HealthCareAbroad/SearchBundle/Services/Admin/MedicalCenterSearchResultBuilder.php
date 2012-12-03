<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

class MedicalCenterSearchResultBuilder extends SearchResultBuilder
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