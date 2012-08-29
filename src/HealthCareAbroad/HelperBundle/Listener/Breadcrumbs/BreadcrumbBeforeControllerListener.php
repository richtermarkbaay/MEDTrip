<?php
/**
 * Listener for kernel.controller for creating breadcrumbs of current request
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\HelperBundle\Listener\Breadcrumbs;

use \Exception;

use \Twig_Environment;

use HealthCareAbroad\HelperBundle\Services\BreadcrumbTreeService;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

abstract class BreadcrumbBeforeControllerListener
{
    /**
     * @var string
     */
    protected $templateName;
    
    /**
     * @var string
     */
    protected $matchedRoute;
    
    protected $request;
    
    /**
     * @var BreadcrumbTreeService
     */
    protected $breadcrumbTreeService;
    
    /**
     * @var \Twig_Environment
     */
    protected $twig;
    
    /**
     * @var Symfony\Bundle\FrameworkBundle\Routing\Router
     */
    protected $router;
    
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
     * Specific validation for breadcrumb context
     * 
     * @return boolean
     */
    abstract protected function validate();
    
    
    /**
     * kernel.controller listener method
     * 
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $this->request = $event->getRequest();
        $this->matchedRoute = $this->request->get('_route');
        
        if ($this->request->isXmlHttpRequest()) {
            // this is an Ajax request we do not need to have breadcrumbs here
            return;
        }
        
        if (!$this->validate()) {
            return;
        }
        
        // find a matching breadcrumb node by route
        $node = $this->breadcrumbTreeService->getNodeByRoute($this->matchedRoute);
        if ($node) {
            
            $routeObj = $this->router->getRouteCollection()->get($this->matchedRoute);
            $compiledRoute = $routeObj->compile();
            $commonParams = $this->request->get('_route_params');
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
            $breadcrumbs = $this->twig->render($this->templateName, array(
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