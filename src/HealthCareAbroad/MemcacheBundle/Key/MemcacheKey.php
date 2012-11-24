<?php
namespace HealthCareAbroad\MemcacheBundle\Key;

class MemcacheKey
{
    protected $pattern;
    
    protected $namespaces;
    
    protected $variables;
    
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        
        return $this;
    }
    
    public function setVariables($variables)
    {
        $this->variables = $variables;
        
        return $this;
    }
    
    public function getVariables()
    {
        return $this->variables;
    }
    
    public function getPattern()
    {
        return $this->pattern;
    }
    
    public function addNamespace(MemcacheNamespace $namespace)
    {
        $this->namespaces[$namespace->getName()] = $namespace;
    }
    
    public function getNamespaces()
    {
        return $this->namespaces;
    }
    
    public function setParameters($parameters)
    {
        
    }
    
}