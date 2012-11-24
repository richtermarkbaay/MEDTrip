<?php
namespace HealthCareAbroad\MemcacheBundle\Services;

use HealthCareAbroad\MemcacheBundle\Key\MemcacheKey;

use HealthCareAbroad\MemcacheBundle\Key\ConfigurationDefaults;

use HealthCareAbroad\MemcacheBundle\Exception\MemcacheCollectionException;

use HealthCareAbroad\MemcacheBundle\Key\MemcacheNamespace;

use HealthCareAbroad\MemcacheBundle\Key\MemcacheKeyCollection;

use HealthCareAbroad\MemcacheBundle\Key\MemcacheNamespaceCollection;

/**
 * Compiler class for memcache keys and memcache namespaces configuration
 * 
 * @author Allejo Chris G. Velarde
 */
class KeyCompiler
{
    private $configMemcacheNamespaces;
    
    private $configMemcacheKeys;
    
    private $namespaceCollection;
    
    private $keyCollection;
    
    public function __construct($configMemcacheNamespaces, $configMemcacheKeys)
    {
        $this->namespaceCollection = new MemcacheNamespaceCollection();
        $this->keyCollection = new MemcacheKeyCollection();
        
        $this->configMemcacheNamespaces = $configMemcacheNamespaces;
        $this->_prepareMemcacheNamespaceCollection();
        
        $this->configMemcacheKeys = $configMemcacheKeys;
        $this->_prepareMemcacheKeyCollection();
    }
    
    /**
     * @return \HealthCareAbroad\MemcacheBundle\Key\MemcacheKeyCollection
     */
    public function getMemcacheKeyCollection()
    {
        return $this->keyCollection;
    }
    
    /**
     * @return \HealthCareAbroad\MemcacheBundle\Key\MemcacheNamespaceCollection
     */
    public function getMemcacheNamespaceCollection()
    {
        return $this->namespaceCollection;
    }
    
    /**
     * Interpolate variables defined in a regex pattern
     * 
     * @param string $pattern
     * @param array $patternVariables
     * @param array $suppliedValues
     * @throws \Exception
     * @return unknown
     */
    public function interpolatePatternVariables($pattern, $patternVariables, $suppliedValues)
    {
        foreach ($patternVariables as $key => $match) {
            if (!\array_key_exists($key, $suppliedValues)) {
                // a declared parameter in the memcache key pattern has no passed value
                throw new \Exception("Cannot generate memcache key with missing required parameter {$key}.");
            }
            $pattern = \str_replace($match['replace_string'], $suppliedValues[$key], $pattern);
        }
        
        return $pattern;
    }
    
    private function _setupMemcacheKeyVariables($pattern)
    {
        preg_match_all('/\{(\w+)\}/', $pattern, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        $variables = array();
        foreach ($matches as $match){
            $variableName = $match[1][0];
            if (\array_key_exists($variableName, $variables)) {
                throw MemcacheCollectionException::duplicatePatternParameter($pattern, $variableName);
            }
            
            $variables[$variableName] = array(
                'pattern' => $match[0][0],
                'replace_string' => $match[0][0],
                'start_offset' => $match[0][1]
            );
        }
        return $variables;
    }
    
    private function _prepareMemcacheNamespaceCollection()
    {
        foreach ($this->configMemcacheNamespaces as $key => $configuration) {
            
            if ($this->namespaceCollection->has($key)) {
                throw MemcacheCollectionException::duplicateNamespace($key);
            }
            
            if (!$this->_hasConfiguration('pattern',$configuration)) {
                throw MemcacheCollectionException::missingRequiredNamespaceParameter($key, 'prefix');
            }
            
            $namespace = new MemcacheNamespace();
            $namespace->setName($key);
            $namespace->setPattern($configuration['pattern']);
            $namespace->setSeparator($this->_getConfiguration('separator', $configuration));
            
            $variables = $this->_setupMemcacheKeyVariables($namespace->getPattern());
            $namespace->setVariables($variables);
            
            $this->namespaceCollection->set($key, $namespace);
        }
        
    }
    
    private function _prepareMemcacheKeyCollection()
    {
        foreach ($this->configMemcacheKeys as $key => $configuration) {
            
            if ($this->keyCollection->has($key)) {
                throw MemcacheCollectionException::duplicateMemcacheKey($key);
            }
            
            if (!$this->_hasConfiguration('pattern', $configuration)) {
                throw MemcacheCollectionException::missingRequiredMemcacheKeyConfig($key, 'pattern');
            }
            
            $memcacheKey = new MemcacheKey();
            $memcacheKey->setPattern($configuration['pattern']);
            $namespaces = $this->_getConfiguration('namespaces', $configuration, array());
            foreach ($namespaces as $eachNamespace) {
                if (!$this->namespaceCollection->has($eachNamespace)) {
                    throw new MemcacheCollectionException("Invalid namespace {$eachNamespace} for memcache key {$key}");
                }    
                $memcacheKey->addNamespace($this->namespaceCollection->get($eachNamespace));
            }
            
            // compile pattern regexpression to setup pattern variables
            $variables = $this->_setupMemcacheKeyVariables($memcacheKey->getPattern());
            $memcacheKey->setVariables($variables);
            
            // add to collection
            $this->keyCollection->set($key, $memcacheKey);
        }
    }
    
    private function _hasConfiguration($key, $configuration=array())
    {
        return \array_key_exists($key, $configuration) && !\is_null($configuration[$key]);
    }
    
    /**
     * Convenience function to get config of $key if $key exists in configuration. If $key does not exists, return $default
     * 
     * @param string $key
     * @param array $configuration
     * @param mixed $default
     */
    private function _getConfiguration($key, $configuration = array(), $default=null)
    {
        return $this->_hasConfiguration($key, $configuration)
            ? $configuration[$key]
            : $default; 
    }
}