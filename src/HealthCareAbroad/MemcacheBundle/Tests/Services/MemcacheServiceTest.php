<?php

namespace HealthCareAbroad\MemcacheBundle\Tests\Services;

use HealthCareAbroad\MemcacheBundle\Tests\MemcacheBundleUnitTestCase;

class MemcacheServiceTest extends MemcacheBundleUnitTestCase
{
    public function testSet()
    {
        $this->getServiceContainer()->get('services.memcache')->set('my_test_key', array('watasdfsfasf'));
    }
    
    public function testGet()
    {
        $this->getServiceContainer()->get('services.memcache')->get('my_test_key');
    }
}
