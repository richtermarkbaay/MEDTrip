<?php
/*
 * This file is part of the PagerBundle package.
 *
 */
namespace HealthCareAbroad\PagerBundle\Twig\Extension;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Templating\Helper\PagerHelper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * PagerExtension extends Twig with pagination capabilities.
 *
 */
class PagerExtension extends \Twig_Extension
{
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'paginate' => new \Twig_Function_Method($this, 'paginate', array('is_safe' => array('html'))),
            'paginate_path' => new \Twig_Function_Method($this, 'path', array('is_safe' => array('html'))),
        );
    }

    public function paginate(Pager $pager, $route, array $parameters = array(), $template = null)
    {
        $template = $template ?: $this->container->getParameter('chromedia_pager.pager.template') ?: 'PagerBundle:Pager:paginate.html.twig';

        return $this->container->get('chromedia_pager.templating.helper.pager')->paginate(
            $pager,
            $route,
            $parameters,
            $template
        );
    }

    public function path($route, $page, array $parameters = array())
    {
        return $this->container->get('chromedia_pager.templating.helper.pager')->path(
            $route,
            $page,
            $parameters
        );
    }

    public function getName()
    {
        return 'pager';
    }
}