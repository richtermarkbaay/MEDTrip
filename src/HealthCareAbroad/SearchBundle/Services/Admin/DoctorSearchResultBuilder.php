<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

class DoctorSearchResultBuilder extends SearchResultBuilder
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