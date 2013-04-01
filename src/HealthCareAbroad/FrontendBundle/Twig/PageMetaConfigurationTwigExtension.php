<?php

namespace HealthCareAbroad\FrontendBundle\Twig;

use ChromediaUtilities\Helpers\Inflector;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration;

use HealthCareAbroad\HelperBundle\Services\PageMetaConfigurationService;

use Symfony\Component\HttpFoundation\Request;

class PageMetaConfigurationTwigExtension extends \Twig_Extension
{
    /**
     * @var PageMetaConfigurationService
     */
    private $pageMetaConfigurationService;
    
    public function setPageMetaConfigurationService(PageMetaConfigurationService $v)
    {
        $this->pageMetaConfigurationService = $v;
    }
    
    public function getName()
    {
        return 'frontendPageMetaConfigurationExtension';
    }
    
    public function getFunctions()
    {
        return array(
            'build_frontend_meta_configuration' => new \Twig_Function_Method($this, 'build_frontend_meta_configuration')
        );
    }
    
    public function build_frontend_meta_configuration(Request $request)
    {
        $url = $request->getPathInfo();
        $metaConfig = $this->pageMetaConfigurationService->findOneByUrl($url);
        // we have no saved configuration yet
        if (!$metaConfig) {
            // TODO: this is just a temporary approach
            // check if searchObjects attributes parameter has been set from the main controller
            if ($request->attributes->has('searchObjects')) {
                // this is a search results page
                $metaConfig = $this->pageMetaConfigurationService->buildFromSearchObjects($request->attributes->get('searchObjects'));
                $metaConfig->setUrl($url);
                
                // save this new config
                $this->pageMetaConfigurationService->save($metaConfig);
            }
            // institution/hospital page
            elseif(($institution = $request->attributes->get('institution', null)) && $institution instanceof Institution && PageMetaConfiguration::PAGE_TYPE_INSTITUTION == $request->attributes->get('pageMetaContext', PageMetaConfiguration::PAGE_TYPE_STATIC)) {
                $metaConfig = $this->pageMetaConfigurationService->buildForInstitutionPage($institution);
                $metaConfig->setUrl($url);
                
                // save this new config
                $this->pageMetaConfigurationService->save($metaConfig);
            }
            else {
                // we don't have dynamic ways for adding other pages
                $metaConfig = new PageMetaConfiguration();
                // set to default metas
                $metaConfig->setTitle('HealthcareAbroad.com - Global Medical Tourism Directory');
                $metaConfig->setDescription('');
                $metaConfig->setKeywords('');
            }
        }
        
        $pageMetaVariables = $request->attributes->get('pageMetaVariables', array());
        
        // replace variables in description
        $metaConfig->setDescription($this->_supplyPatternVariables($metaConfig->getDescription(), $pageMetaVariables));
        // replace variables in keywords
        $metaConfig->setKeywords($this->_supplyPatternVariables($metaConfig->getKeywords(), $pageMetaVariables));
        
        return $metaConfig;
    }
    
    private function _supplyPatternVariables($subject, array $values)
    {
        return Inflector::supplyPatternVariableValues($subject, $values);
    }
}