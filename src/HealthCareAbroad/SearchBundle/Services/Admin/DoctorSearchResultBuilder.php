<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

class DoctorSearchResultBuilder extends SearchResultBuilder
{
    private function buildQueryBuilder()
    {
        
    }
    
    protected function buildResult()
    {
        $result = new AdminSearchResult();
        $result->setName();
    }
}