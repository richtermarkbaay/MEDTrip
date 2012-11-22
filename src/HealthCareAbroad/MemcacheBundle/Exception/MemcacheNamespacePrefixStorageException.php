<?php
namespace HealthCareAbroad\MemcacheBundle\Exception;

class MemcacheNamespacePrefixStorageException extends \Exception
{
    static public function invalidBaseKey($configBaseKey)
    {
        return new self("Invalid memcache config base key {$configBaseKey}.");
    }    
}