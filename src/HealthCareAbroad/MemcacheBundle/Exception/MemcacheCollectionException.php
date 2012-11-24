<?php
namespace HealthCareAbroad\MemcacheBundle\Exception;

class MemcacheCollectionException extends \Exception
{
    static public function invalidMemcacheKey($key)
    {
        return new self("Unable to load memcache key {$key}.");
    }
    
    static public function duplicateMemcacheKey($key)
    {
        return new self("Cannot redeclare memcache key {$key}.");
    }
    
    static public function duplicatePatternParameter($pattern, $parameter)
    {
        return new self(sprintf('Memcache key pattern "%s" cannot reference variable name "%s" more than once.', $pattern, $parameter));
    }
    
    static public function missingRequiredMemcacheKeyConfig($mecacheKey, $config)
    {
        return new self("Configuration {$config} of memcache key {$mecacheKey} is required in memcache keys configuration");
    }
    
    static public function missingRequiredNamespaceParameter($namespace, $configKey)
    {
        return new self("Configuration {$configKey} of memcache namespace {$namespace} is required in memcache namespaces configuration");
    }
    
    static public function invalidNamespace($id)
    {
        return new self("Unable to load memcache namespace {id}.");
    }
    
    static public function duplicateNamespace($id)
    {
        return new self("Cannot redeclare namespace {$id}.");
    }
}