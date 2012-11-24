<?php
namespace HealthCareAbroad\MemcacheBundle\Services;

use ChromediaUtilities\Helpers\SecurityHelper;

use HealthCareAbroad\MemcacheBundle\Exception\KeyFactoryException;

use HealthCareAbroad\MemcacheBundle\Key\MemcacheKeyCollection;
/**
 * Factory class for memcache keys
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class KeyFactory
{
    
    const MEMCACHE_GENERATED_KEY_STORAGE_PREFIX = 'hca_memcache_generated_keys';
    
    /**
     * @var KeyCompiler
     */
    private $compiler;
    
    /**
     * @var MemcacheKeyCollection
     */
    private $memcacheKeys;
    
    /**
     * @var MemcacheService
     */
    private $memcache;
    
    /**
     * Inject KeyCompiler
     * 
     * @param KeyCompiler $compiler
     */
    public function setCompiler(KeyCompiler $compiler)
    {
        $this->compiler = $compiler;
        $this->memcacheKeys = $this->compiler->getMemcacheKeyCollection();   
    }
    
    /**
     * Inject MemcacheService
     * 
     * @param MemcacheService $memcache
     */
    public function setMemcache(MemcacheService $memcache)
    {
        $this->memcache = $memcache;
    }
    
    /**
     * Generate a memcache key using a configuration key
     * 
     * @param string $key the config memecache key name
     * @param array $memcacheKeyParameters
     * @param array $memcacheNamespaceParameters
     * @throws KeyFactoryException if $key does not exist in MemcacheKeyCollection
     * @author acgvelarde
     */
    public function generate($key, $memcacheKeyParameters=array(), $memcacheNamespaceParameters=array())
    {  
        if (!$this->memcacheKeys->has($key)) {
            // key is unrecognizable in our compiled collection
            throw new KeyFactoryException("Cannot generate invalid memcache key {$key}");
        }
        
        $memcacheKey = $this->memcacheKeys->get($key);
        $tempPattern = $memcacheKey->getPattern(); // set the pattern to temporary string for manipulation
        
        // interpolate the variables of the memcache key pattern with $memcacheKeyParameters
        $memcacheKeyString = $this->compiler->interpolatePatternVariables($memcacheKey->getPattern(), $memcacheKey->getVariables(), $memcacheKeyParameters);
        
        $memcacheNamespaceKeys = array(); // an array holding the processed memcache namespace patterns
        // prepare the memcache namespaces
        foreach ($memcacheKey->getNamespaces() as $memcacheNamespace) {
            
            // interpolate the variables in the namespace key regex pattern
            $memcacheNamespaceKeyString = $this->compiler->interpolatePatternVariables($memcacheNamespace->getPattern(), $memcacheNamespace->getVariables(), $memcacheNamespaceParameters);
             
            // get version of memcache namespace
            $version = $this->memcache->get($memcacheNamespaceKeyString);
            if (!$version) {
                // generate new version for this namespace and save it to memcache
                $version = \time();
                $this->memcache->set($memcacheNamespaceKeyString, $version);
            }
            // append the version to this memcache namespace
            $memcacheNamespaceKeyString .= '_'.$version;
            $memcacheNamespaceKeys[] = $memcacheNamespaceKeyString;
        }
        
        $memcacheKeyString = $memcacheKeyString . '_'. \implode('_',$memcacheNamespaceKeys);
        
        // check if this key has been generated and saved to memcache before
        $generatedKeyStorageKey = self::MEMCACHE_GENERATED_KEY_STORAGE_PREFIX.'_'.$memcacheKeyString;
        $storedMemcachekey = $this->memcache->get($generatedKeyStorageKey);
        if (!$storedMemcachekey) {
            // no stored key, let's save this key
            // hash the string so we can have equal lengths of memcache keys
            $memcacheKeyString = SecurityHelper::hash_sha256($memcacheKeyString.time());
            $this->memcache->set($generatedKeyStorageKey, $memcacheKeyString);
        }
        else {
            $memcacheKeyString = $storedMemcachekey;
        }
        
        return $memcacheKeyString;
    }
}

