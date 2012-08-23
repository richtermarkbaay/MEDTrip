<?php
/**
 * Listener for kernel.controller for creating breadcrumbs of current request
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Listener;

use \Twig_Environment;

use HealthCareAbroad\HelperBundle\Services\BreadcrumbTreeService;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class AdminBreadcrumbBeforeControllerListener
{
    /**
     * @var BreadcrumbTreeService
     */
    private $breadcrumbTreeService;
    
    /**
     * @var \Twig_Environment
     */
    private $twig;
    
    public function setBreadcrumbService($service)
    {
        $this->breadcrumbTreeService = $service;
    }
    
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
    
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();
        $matched_route = $request->get('_route');
        
        
        // this check is only based on convention that all admin routes start with admin
        if (!\preg_match('/^admin/', $matched_route)) {
            // non-admin route, do nothing
            return;
        }
        
        // find a matching breadcrumb node by route
        $node = $this->breadcrumbTreeService->getNodeByRoute($matched_route);
        if ($node) {
            // render the template for the breadcrumbs
            $breadcrumbs = $this->twig->render('AdminBundle:Default:breadcrumbs.html.twig', array(
                'currentNode' => $node,
                'ancestors' => $node->getAncestors(),
                'routeParams' => $request->get('_route_params')
            ));
        }
        else {
            $breadcrumbs = null;
        }
        $this->twig->addGlobal('breadcrumbs', $breadcrumbs);
    }    
}