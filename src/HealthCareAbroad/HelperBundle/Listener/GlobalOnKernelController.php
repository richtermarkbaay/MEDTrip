<?php

/**
 * @author Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Listener;

use HealthCareAbroad\StatisticsBundle\Services\StatisticsService;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;


class GlobalOnKernelController
{
    /**
     * 
     * @param Twig_Environment
     */
    protected $twig;

    /** 
     * @var StatisticsService
     */
    protected $statsService;
    

    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
    
    /**
     * 
     * @param StatisticsService $statsService
     */
    public function setStatisticsService(StatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * kernel.response listener method
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');


        // Set Frontend Breadcrumb
        if(substr($route, 0, 8) == 'frontend') {
            //var_dump($request->attributes->get('_route_params'));
            //var_dump($request->attributes);
            //var_dump($route);
            //var_dump("IN! " . $route);
            $this->twig->addGlobal('renderFrontendBreadcrumb', true);           
        }
        // End of Set Frontend Breadcrumb 
        
        // Add Page View Stats
        $this->statsService->addPageViewStats($request);

//echo $route;
        
    }
}