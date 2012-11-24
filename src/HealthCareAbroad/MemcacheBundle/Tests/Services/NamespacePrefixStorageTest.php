<?php

namespace HealthCareAbroad\MemcacheBundle\Tests\Services;

use HealthCareAbroad\MemcacheBundle\Tests\MemcacheBundleUnitTestCase;

class NamespacePrefixStorageTest extends MemcacheBundleUnitTestCase
{
    public function testOnly()
    {
        
        $actual = 'institution_1:1234423_entity_1:12312341_1_profile';
        $matches = array();
        $pattern = 'institution_{institutionBaseNamespaceVersion}_entity_{anotherNamespaceVersion}_{id}_profile';
        
        preg_match_all('/\{(\w+)\}/', $pattern, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        var_dump($matches); exit;
        
    }
}