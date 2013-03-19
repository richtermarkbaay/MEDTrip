<?php

namespace HealthCareAbroad\SearchBundle\Services;

/**
 * TODO: not yet implemented in frontend search
 * 
 * @author Allejo Chris G. Velarde
 */
use HealthCareAbroad\TermBundle\Entity\TermDocument;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use HealthCareAbroad\SearchBundle\Exception\SearchWidgetException;

use Symfony\Component\HttpFoundation\Request;

class SearchParameterService
{
    const CONTEXT_BROAD_SEARCH = 1;
    
    const CONTEXT_NARROW_SEARCH = 2;
    
    // widget context identifier
    const PARAMETER_KEY_CONTEXT = 'hcaSearchContext';
    
    const PARAMETER_KEY_COUNTRY_ID = 'countryId';
    
    const PARAMETER_KEY_CITY_ID = 'cityId';
    
    const PARAMETER_KEY_SPECIALIZATION_ID = 'specializationId';
    
    const PARAMETER_KEY_SUB_SPECIALIZATION_ID = 'subSpecializationId';
    
    const PARAMETER_KEY_TREATMENT_ID = 'treatmentId';
    
    const PARAMETER_KEY_TERM_ID = 'termId';
    
    /**
     * @var SearchParameterCompiler
     */
    private $compiler;
    
    public function setCompiler(SearchParameterCompiler $v)
    {
        $this->compiler = $v;
    }
    
    /**
     * Convert request parameters to SearchParameterBag
     * 
     * @param array $requestParameters
     * @return SearchParameterBag
     */
    public function getParameterBag(Request $request)
    {
        $context = $request->get(self::PARAMETER_KEY_CONTEXT, 0);
        $parameterBag = null;
        switch ($context) {
            case self::CONTEXT_BROAD_SEARCH:
                $parameterBag = new BroadSearchParameterBag($request->get('broadSearch', array()));
                break;
            case self::CONTEXT_NARROW_SEARCH:
                $parameterBag = new NarrowSearchParameterBag($request->get('narrowSearch', array()));
                break;
            default:
                throw SearchWidgetException::unknownContext($context); // unknown context
        }
        
        return $parameterBag;
    }
    
    public function compileRequest(Request $request)
    {
        return $this->compiler->compile($this->getParameterBag($request));
    }
    
    
    /**
     * Convenience function to check if passed context is a known context
     * 
     * @param int $context
     * @return boolean
     */
    public static function isKnownContext($context)
    {
        $knownContexts = array(SearchParameterService::CONTEXT_BROAD_SEARCH, SearchParameterService::CONTEXT_NARROW_SEARCH);
        return \in_array($context, $knownContexts);
    }
    
    public static function getBroadSearchParameterKeys()
    {
        $keys = array();
        foreach (BroadSearchParameterBag::getAllowedParameters() as $key) {
            $keys[$key] = 'broadSearch['.$key.']';
        }
        
        return $keys;
    }
    
    public static function getNarrowSearchParameterKeys()
    {
        $keys = array();
        foreach (NarrowSearchParameterBag::getAllowedParameters() as $key) {
            $keys[$key] = 'narrowSearch['.$key.']';
        }
        
        return $keys;
    }
}