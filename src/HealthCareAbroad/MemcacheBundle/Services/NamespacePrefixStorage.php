<?php
namespace HealthCareAbroad\MemcacheBundle\Services;

use HealthCareAbroad\MemcacheBundle\Exception\MemcacheNamespacePrefixStorageException;

/**
 * Storage service for Memcache namespace prefixes
 * Accessble through service id services.memcache.namespace_prefix_storage
 * 
 * @author Allejo Chris G. Velarde
 */
class NamespacePrefixStorage
{
    protected $basePrefixes = array();
    
    /**
     * @var MemcacheService
     */
    protected $memcacheService;
    
    public function setBasePrefixes($basePrefixes)
    {
        $this->basePrefixes = $basePrefixes;
    }
    
    /**
     * Set the MemcacheService
     * @param MemcacheService $memcache
     */
    public function setMemcacheService(MemcacheService $memcache)
    {
        $this->memcacheService = $memcache;
    }
    
    /**
     * Get the memcache service used
     * 
     * @return \HealthCareAbroad\MemcacheBundle\Services\MemcacheService
     */
    public function getMemcacheService()
    {
        return $this->memcacheService;
    }
    
    public function isValidBaseKey($configBaseKey)
    {
        
        return \array_key_exists($configBaseKey, $this->basePrefixes);
    }

    /**
     * Get namespace key 
     * 
     * @param string $configBaseKey
     * @param string $uniqueIdentifier
     */
    public function getNamespaceKey($configBaseKey, $uniqueIdentifier)
    {
        if (!$this->isValidBaseKey($configBaseKey)) {
            throw MemcacheNamespacePrefixStorageException::invalidBaseKey($configBaseKey);
        }
        
        $namespaceKey = $this->basePrefixes[$configBaseKey].'_'.$uniqueIdentifier;
        
        return $namespaceKey;
    }
    
    public function getNamespaceByConfigKey($configBaseKey, $uniqueIdentifier)
    {
        $key = $this->getNamespaceKey($configBaseKey, $uniqueIdentifier);
        $version = $this->getNamespaceVersion($key);
        
        return $key.'_v'.$version;
    }
    
    protected function getNamespaceVersion($namespaceKey)
    {
        $version = $this->memcacheService->get($namespaceKey);
        if (!$version) {
            // generate new version for this key
            $version = time();
            $this->memcacheService->set($namespaceKey, $version);
        }
        
        return $version;
    }
    
    
    
    /**
     * Invalidate cached values that are using this namespace 
     * 
     * @param string $namespaceKey
     */
    public function invalidateNamespace($namespaceKey)
    {
        if (!$this->memcacheService->increment($namespaceKey)) {
            // failed to increment value of namespace with key $namespaceKey
            
        }
    }
}