<?php

namespace HealthCareAbroad\MemcacheBundle\Tests\Services;

use HealthCareAbroad\MemcacheBundle\Tests\MemcacheBundleUnitTestCase;

class MemcacheServiceTest extends MemcacheBundleUnitTestCase
{

    private $key = 'my_test_key';

    private $value = array('this is a test value', 'another key' => 'dsfadsfasfsaf');

    public function testSet()
    {
        $returnVal = $this->getServiceContainer()->get('services.memcache')->set($this->key, $this->value);

        $this->assertInstanceOf('HealthCareAbroad\MemcacheBundle\Services\MemcacheService', $returnVal, 'Expecting HealthCareAbroad\MemcacheBundle\Services\MemcacheService to be return after storing a value to memcached server. This is bound to fail if no Memcache client has been installed.');
    }

    /**
     * @depends testSet
     */
    public function testGet()
    {
        $returnVal = $this->getServiceContainer()->get('services.memcache')->get($this->key);

        $this->assertEquals($this->value, $returnVal, 'Expecting return value of get be equal to the stored value.');
    }
}
