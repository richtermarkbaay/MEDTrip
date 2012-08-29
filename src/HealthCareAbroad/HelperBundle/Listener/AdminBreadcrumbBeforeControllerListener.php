<?php
/**
 * Listener for kernel.controller for creating breadcrumbs of current request
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Listener;

use \Exception;

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
    
    /**
     * @var Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    private $router;
    
    public function setBreadcrumbService($service)
    {
        $this->breadcrumbTreeService = $service;
    }
    
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
    
    public function setRouter($router)
    {
        $this->router = $router;
    }
    
    /**
     * kernel.controller listener method
     * 
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        
        $controller = $event->getController();
        $request = $event->getRequest();
        $matchedRoute = $request->get('_route');
        
        if ($request->isXmlHttpRequest()) {
            // this is an Ajax request we do not need to have breadcrumbs here
            return;
        }
        
        // this check is only based on convention that all admin routes start with admin
        if (!\preg_match('/^admin/', $matchedRoute)) {
            // non-admin route, do nothing
            return;
        }
        
        
        
        // find a matching breadcrumb node by route
        $node = $this->breadcrumbTreeService->getNodeByRoute($matchedRoute);
        if ($node) {
            
            $routeObj = $this->router->getRouteCollection()->get($matchedRoute);
            $compiledRoute = $routeObj->compile();
            
            $commonParams = $request->get('_route_params');
            
            $ancestorItems = array();
            $ancestors = $this->breadcrumbTreeService->getPathOfNode($node, false);
            foreach ($ancestors as $breadcrumbObj) {
                $breadcrumbItem = array(
                    'name' => $breadcrumbObj->getLabel(),
                    'href' => null,
                    'params' => array()
                );
                
                // compile the route for each node
                if ($routeObj = $this->router->getRouteCollection()->get($breadcrumbObj->getRoute())) {
                    $compiledRoute = $routeObj->compile();
                    // use key intersect to get the common request parameters in this current Request context and this node
                    $intersectedParams = \array_intersect_key($commonParams, \array_flip($compiledRoute->getVariables()));
                    $breadcrumbItem['params'] = $intersectedParams;
                    try {
                        $breadcrumbItem['href'] = $this->router->generate($breadcrumbObj->getRoute(),$intersectedParams);
                    }
                    catch (\Exception $e) {
                        // do nothing here since we will only display the breadcrumb label if there is no generated link
                    }
                }
                
                $ancestorItems[] = $breadcrumbItem;
            }
            
            
            // render the template for the breadcrumbs
            $breadcrumbs = $this->twig->render('AdminBundle:Default:breadcrumbs.html.twig', array(
                'currentNode' => $node,
                'ancestorItems' => $ancestorItems,
            ));
        }
        else {
            $breadcrumbs = null;
        }
        $this->twig->addGlobal('breadcrumbs', $breadcrumbs);
    }    
}