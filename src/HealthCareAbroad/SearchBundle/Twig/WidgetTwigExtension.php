<?php

namespace HealthCareAbroad\SearchBundle\Twig;

use HealthCareAbroad\SearchBundle\Services\SearchService;

use HealthCareAbroad\SearchBundle\Exception\SearchWidgetException;

use HealthCareAbroad\SearchBundle\Services\SearchParameterService;

class WidgetTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     *
     * @var SearchService
     */
    private $searchService;

    public function setTwig($v)
    {
        $this->twig = $v;
    }

    public function setSearchService(SearchService $service)
    {
        $this->searchService = $service;
    }

    public function getName()
    {
        return 'search_widget_twig_extension';
    }

    public function getFunctions()
    {
        return array(
            'render_search_homepage_widget' => new \Twig_Function_Method($this, 'render_search_homepage_widget'),
            'render_narrow_search_widget' => new \Twig_Function_Method($this, 'render_narrow_search_widget'),
            'render_admin_custom_search_widget' => new \Twig_Function_Method($this, 'render_admin_custom_search_widget'),
            'get_broadsearch_parameter_keys' => new \Twig_Function_Method($this, 'get_broadsearch_parameter_keys'),
            'render_broad_search_context_parameter' => new \Twig_Function_Method($this, 'render_broad_search_context_parameter'),
            'render_narrow_search_context_parameter' => new \Twig_Function_Method($this, 'render_narrow_search_context_parameter'),
        );
    }

    public function render_broad_search_context_parameter()
    {
        return $this->_renderSearchContextParameter(SearchParameterService::CONTEXT_BROAD_SEARCH);
    }

    public function render_narrow_search_context_parameter()
    {

    }

    private function _renderSearchContextParameter($context)
    {
        if (!SearchParameterService::isKnownContext($context)) {
            throw SearchWidgetException::unknownContext($context);
        }

        return '<input type="hidden" name="'. SearchParameterService::PARAMETER_KEY_CONTEXT.'" value="'.$context.'" />';
    }

    public function get_broadsearch_parameter_keys()
    {
        return SearchParameterService::getBroadSearchParameterKeys();
    }

    /**
     * TODO - need to update HTML.
     * @param array $options
     */
    public function render_admin_custom_search_widget(array $options=array(), $preloadSearchTerms = false)
    {
        return $this->render_search_homepage_widget($options, 'SearchBundle:SearchForms:admin.customsearch.html.twig', $preloadSearchTerms);
    }

    public function render_search_homepage_widget(array $options=array(), $twigTemplate = null, $preloadSearchTerms = false)
    {
        $twigTemplate = \is_null($twigTemplate) ? 'SearchBundle:SearchForms:form.homepage.html.twig' : $twigTemplate;
        $defaultOptions = array('attr' => array());
        $options = array_merge($defaultOptions, $options);
        $params = $options;

        if ($preloadSearchTerms) {
            $params['treatments'] = json_encode($this->searchService->getAllSpecializations());
            $params['destinations'] = json_encode($this->searchService->getAllDestinations());
        }

        return $this->twig->render($twigTemplate, $params);
    }

    public function render_narrow_search_widget(array $widgets, array $parameters = array(), $twigTemplate=null)
    {
        $treatmentsConfig = array(
            'specialization' => array(
                'label' => 'Specialization'),
            'sub_specialization' => array('label' => 'Sub-specialization'),
            'treatment' => array('label' => 'Treatment')
        );
        $destinationsConfig = array(
            'country' => array('label' => 'Country'),
            'city' => array('label' => 'City'),
            'destinations' => array('label' => 'Destination')
        );

        $treatmentWidgets = \array_intersect_key($treatmentsConfig, \array_flip($widgets));
        $destinationWidgets = \array_intersect_key($destinationsConfig, \array_flip($widgets));

        $twigTemplate = \is_null($twigTemplate) ? 'SearchBundle:SearchForms:sidebar.narrowsearch.html.twig' : $twigTemplate;
        return $this->twig->render($twigTemplate, array(
            'treatmentWidgets' => $treatmentWidgets,
            'destinationWidgets' => $destinationWidgets,
            'widget_keys' => \array_keys(\array_merge($treatmentWidgets, $destinationWidgets)),
            'currentParameters' => $parameters
        ));
    }
}