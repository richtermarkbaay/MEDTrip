<?php

namespace HealthCareAbroad\SearchBundle\Twig;

class WidgetTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    private $twig;
    
    public function setTwig($v)
    {
        $this->twig = $v;
    }
    
    public function getName()
    {
        return 'search_widget_twig_extension';
    }
    
    public function getFunctions()
    {
        return array(
            'render_search_homepage_widget' => new \Twig_Function_Method($this, 'render_search_homepage_widget'),
            'render_narrow_search_widget' => new \Twig_Function_Method($this, 'render_narrow_search_widget')
        );
    }
    
    public function render_search_homepage_widget(array $options=array(), $twigTemplate = null)
    {
        $twigTemplate = \is_null($twigTemplate) ? 'SearchBundle:SearchForms:form.homepage.html.twig' : $twigTemplate;
        
        return $this->twig->render($twigTemplate);
    }
    
    public function render_narrow_search_widget($widgets)
    {
        
    }
}