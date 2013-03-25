<?php

namespace HealthCareAbroad\FrontendBundle\Twig;

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
            else {
                // we don't have dynamic ways for adding other pages
                $metaConfig = new PageMetaConfiguration();
                // set to default metas
                $metaConfig->setTitle('HealthcareAbroad.com - Global Medical Tourism Directory');
                $metaConfig->setDescription('An international comprehensive and unbiased worldwide directory of Healthcare and Dental Clinics and medical providers abroad.');
                $metaConfig->setKeywords('abroad, medical tourism, cosmetic, dental, dentists, doctors, treatment, surgery, compare, travel');
            }
        }
        
        // replace variables in description
        $tempDescription = $metaConfig->getDescription();
        $pageMetaVariables = $request->attributes->get('pageMetaVariables', array());
        \preg_match_all('/\{.*?\}/', $tempDescription, $matches);
        foreach ($matches[0] as $_matched_pattern) {
            $variable = \preg_replace('/[\{\}]/', '', $_matched_pattern);
            if (\in_array($variable, PageMetaConfigurationService::getKnownVariables())) {
                $value = \array_key_exists($variable, $pageMetaVariables) ? $pageMetaVariables[$variable] : ''; 
                $tempDescription = \preg_replace('/'.$_matched_pattern.'/', $value, $tempDescription);
            }
        }
        $metaConfig->setDescription($tempDescription);
        
        return $metaConfig;
    }
}