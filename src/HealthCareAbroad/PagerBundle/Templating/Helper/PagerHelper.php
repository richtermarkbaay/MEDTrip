<?php
/*
 * This file is part of the PagerBundle package.
 */
namespace HealthCareAbroad\PagerBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Routing\RouterInterface;
use HealthCareAbroad\PagerBundle\Pager;

/**
 * PagerHelper
 */
class PagerHelper extends Helper
{
    /**
     *
     * @var EngineInterface
     */
    protected $engine;
    /**
     *
     * @var RouterInterface
     */
    protected $router;

    /**
     * Constructor
     *
     * @param EngineInterface $engine The template engine service
     * @param RouterInterface $router The router service
     */
    public function __construct(EngineInterface $engine, RouterInterface $router)
    {
        $this->engine = $engine;

        $this->router = $router;
    }

    /**
     * Renders the HTML for a given pager
     *
     * @param Pager $pager A Pager instance
     * @param string $route The route name
     * @param array $parameters Additional route parameters
     * @param string $template The template name
     * @return string The html markup
     */
    public function paginate(Pager $pager, $route, array $parameters = array(), $template = 'ChromediaPagerBundle:Pager:paginate.html.php')
    {
        return $this->engine->render($template, array('pager' => $pager, 'route' => $route, 'parameters' => $parameters));
    }

    /**
     * Generates a URL for a given route and page
     *
     * @param string $route Route name
     * @param int $page Page number
     * @param array $parameters Optional route parameters
     * @return string The url path
     */
    public function path($route, $page, array $parameters = array())
    {
        if (isset($parameters['_page'])) {
            $parameters[$parameters['_page']] = $page;

            unset($parameters['_page']);
        } else {
            $parameters['page'] = $page;
        }

        return $this->router->generate($route, $parameters);
    }

    public function getName()
    {
        return 'pager';
    }
}