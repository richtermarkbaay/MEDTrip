<?php

namespace HealthCareAbroad\MemcacheBundle\Tests\Services;

use HealthCareAbroad\MemcacheBundle\Tests\MemcacheBundleUnitTestCase;

class MemcacheServiceTest extends MemcacheBundleUnitTestCase
{
    public function testSet()
    {
        $this->getServiceContainer()->get('services.memcache');
    }
    
    public function testGet()
    {
        $this->getServiceContainer()->get('services.memcache');
    }
}