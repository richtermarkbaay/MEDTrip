<?php
namespace HealthCareAbroad\FrontendBundle\Twig;

use HealthCareAbroad\FrontendBundle\Services\FrontendBreadcrumbService;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementHighlightType;


class BreadcrumbWidgetTwigExtension extends \Twig_Extension
{
    private $twig;

    private $breadcrumbService;

    public function __construct(FrontendBreadcrumbService $service)
    {
        $this->breadcrumbService = $service;
    }

    public function getFunctions()
    {
        return array(
            'render_frontend_breadcrumb' => new \Twig_Function_Method($this, 'render_frontend_breadcrumb')
        );
    }

    public function render_frontend_breadcrumb()
    {
        $breadcrumbs = $this->breadcrumbService->generateBreadcrumbs();

        if(count($breadcrumbs)) {
            //FIXME: inject twig to the extension rather than using breadcrumbService
            $twig = $this->breadcrumbService->container->get('twig');
            $twig->addGlobal('breadcrumbs', $breadcrumbs);
            return $twig->display('FrontendBundle:Widgets:breadcrumbs.html.twig');
        }
    }

    public function getName()
    {
        return 'frontend_breadcrumb_twig_extension';
    }
}