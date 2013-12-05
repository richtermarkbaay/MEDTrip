<?php

namespace HealthCareAbroad\SearchBundle\Services;

/**
 * Compiled format for SearchParameter bag. This class is an internal class which can only be used by SearchParameterCompiler class
 * 
 * @author Allejo Chris G. Velarde
 */
class CompiledSearchParameter
{
    private $variables;
    private $url;
    private $searchState;
    
    public function __construct(array $variables, $url, $searchState)
    {
        $this->variables = $variables;
        $this->url = $url;
        $this->searchState = $searchState;    
    }
    
    public function getUrl($isDebug=false) 
    {
        return $isDebug ? '/app_dev.php/'.$this->url : $this->url;    
    }
    
    public function getVariables()
    {
        return $this->variables;
    }
    
    public function getSearchState()
    {
        return $this->searchState;
    }
    
}