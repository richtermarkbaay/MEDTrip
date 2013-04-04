<?php

namespace HealthCareAbroad\SearchBundle\Services;

use HealthCareAbroad\TermBundle\Entity\TermDocument;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class SearchParameterCompiler implements  ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    public function setContainer(ContainerInterface $container =null)
    {
        $this->container = $container;
    }
    
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * Compile SearchParameterBag
     * 
     * @param SearchParameterBag $parameterBag
     * @return \HealthCareAbroad\SearchBundle\Services\CompiledSearchParameter
     */
    public function compile(SearchParameterBag $parameterBag)
    {
        $urlGenerator = new SearchUrlGenerator();
        $variables = $this->_extractVariables($parameterBag);
        $searchState = 0;
        
        // search form parameter to search url route key mapping
        $searchUrlParameterKeyMapping = SearchUrlGenerator::getSearchParameterKeyToSearchUrlKeyMapping();
        
        // parameter key to search state value mapping
        $searchStateMapping = SearchStates::getSearchParameterKeyMappingToSearchStateValues();
        
        // get the current state of the search based on current variables, prepare also the url parameters for url generator
        foreach ($variables as $key => $variableObj) {
            
            // add the state value for this key
            $searchState += $searchStateMapping[$key];
            
            // map to the route key
            $searchUrlKey = \array_key_exists($key, $searchUrlParameterKeyMapping) ? $searchUrlParameterKeyMapping[$key] : $key;
            // TODO: this will fail if slug will not be used as a URL parameter value
            if (\method_exists($variableObj, 'getSlug')) {
                $urlGenerator->addParameter($searchUrlKey, $variableObj->getSlug());
            }
        }
        
        // route to state mapping
        $routeToStateMapping = SearchStates::getStateToRouteMapping();
        $routeName = $routeToStateMapping[SearchStates::getSearchStateFromValue($searchState)];
        $url = $urlGenerator->generateByRouteName($routeName, false);// generate the url
        $compiledSearch = new CompiledSearchParameter($variables, $url, $searchState);
        
        return $compiledSearch;
    }
    
    private function _extractVariables(SearchParameterBag $parameterBag)
    {
        $variables = array();
        if ($parameterBag instanceof BroadSearchParameterBag) {
            // processing for comination search of brroad search term
            
            // parameters has a termId in the parameters
            
            
            // binary check on state to check if parameters has term id
            if (0 != (BroadSearchParameterBag::STATE_HAS_TERM_ID & $parameterBag->get('state'))) {
                // process term documents
                $termDocuments = $this->container->get('services.search')->getTermDocuments($parameterBag);
                
                //$this->container->get('services.search')->getTermDocumentsByTermName($parameterBag); // find related terms by term label
            
                // term points to specific type of document
                if (1 == count($termDocuments)) {
                    $variables = $this->_extractVariablesFromTermDocument($termDocuments[0]);
                }
                else {
                    // TODO: related terms
                }
            }
            else {
                // check if it has term label
            }//-- end process of termId part
            
            //var_dump(BroadSearchParameterBag::STATE_HAS_DESTINATION_ID & $parameterBag->get('state')); exit;
            // check if parameters has a destination in it
            if (0 != (BroadSearchParameterBag::STATE_HAS_DESTINATION_ID & $parameterBag->get('state'))) {
                
                $variables[SearchParameterService::PARAMETER_KEY_COUNTRY_ID] = $this->_findGenericObjectById('country', $parameterBag->get(SearchParameterService::PARAMETER_KEY_COUNTRY_ID, 0));
                $city = $this->_findGenericObjectById('city', $parameterBag->get(SearchParameterService::PARAMETER_KEY_CITY_ID, 0));
                if ($city) {
                    $variables[SearchParameterService::PARAMETER_KEY_CITY_ID] = $city; 
                }
            }
        }
        
        return $variables;
    }
    
    private function _extractVariablesFromTermDocument($termDocument)
    {
        $variables = array();
        // no matter what type of term, it alwas has a specialization
        $variables[SearchParameterService::PARAMETER_KEY_SPECIALIZATION_ID] = $this->_findGenericObjectById('specialization', $termDocument['specialization_id']);
        if (TermDocument::TYPE_SUBSPECIALIZATION == $termDocument['type']) {
            $variables[SearchParameterService::PARAMETER_KEY_SUB_SPECIALIZATION_ID] = $this->_findGenericObjectById('subSpecialization', $termDocument['document_id']);
        }
        elseif (TermDocument::TYPE_TREATMENT == $termDocument['type']) {
            $variables[SearchParameterService::PARAMETER_KEY_TREATMENT_ID] = $this->_findGenericObjectById('treatment', $termDocument['document_id']);
        }
        
        return $variables;
    }
    
    private function _findGenericObjectById($classKey, $id)
    {
        $obj = null;
        if ($id) {
            $idToObjectMappers = array(
                'country' => array('services.location', 'getCountryById'),
                'city' => array('services.location', 'getCityById'),
                'specialization' =>  array('services.treatment_bundle', 'getSpecialization'),
                'subSpecialization' => array('services.treatment_bundle', 'getSubSpecialization'),
                'treatment' => array('services.treatment_bundle', 'getTreatment'),
            );
    
            if (\array_key_exists($classKey, $idToObjectMappers)) {
                $obj = $this->container->get($idToObjectMappers[$classKey][0])->{$idToObjectMappers[$classKey][1]}($id);
            }
        }
    
        return $obj;
    }
    
    
    
}