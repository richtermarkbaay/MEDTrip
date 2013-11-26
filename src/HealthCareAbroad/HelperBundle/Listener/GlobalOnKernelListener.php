<?php

/**
 * @author Adelbert D. Silla
 */
namespace HealthCareAbroad\HelperBundle\Listener;

use HealthCareAbroad\StatisticsBundle\Services\TrackerFactory;
use HealthCareAbroad\StatisticsBundle\Services\Trackers\Tracker;

use Symfony\Bundle\FrameworkBundle\HttpKernel;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;


class GlobalOnKernelListener
{
    /** 
     * @param Twig_Environment
     */
    protected $twig;

    /** 
     * @var TrackerFactory
     */
    protected $statsTrackerFactory;
    

    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /** 
     * @param TrackerFactory $statsTrackerFactory
     */
    public function setStatisticsFactoryTracker(TrackerFactory $statsTrackerFactory)
    {
        $this->statsTrackerFactory = $statsTrackerFactory;
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
        $route = $request->attributes->get('_route');

        // Set Frontend Breadcrumb
        if(substr($route, 0, 8) == 'frontend') {
            $this->twig->addGlobal('renderFrontendBreadcrumb', true);           
        }
        // End of Set Frontend Breadcrumb 
    }

    /**
     * kernel.response listener method
     * 
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // Add Page View Stats
        if($event->getRequestType() == HttpKernel::MASTER_REQUEST) {
            $statsTracker = $this->statsTrackerFactory->getTrackerByRoute($route);
            if($statsTracker instanceof Tracker) {
                if($data = $statsTracker->createDataFromHttpRequest($request))
                    $statsTracker->save($data);
            }
        }
    }
}