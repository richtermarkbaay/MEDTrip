<?php
namespace HealthCareAbroad\HelperBundle\Listener;

use Symfony\Bundle\FrameworkBundle\HttpKernel;

use HealthCareAbroad\HelperBundle\Services\StaticPageService;

use Symfony\Component\HttpKernel\Exception\FlattenException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class StaticPageRouterListener
{
    /**
     * @var StaticPageService
     */
    private $routerService;

    public function setRouterService(StaticPageService $service)
    {
        $this->routerService = $service;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        // sub requests and ajax requests should not be handled here
        if (HttpKernel::MASTER_REQUEST != $event->getRequestType() || $request->isXmlHttpRequest()) {
            
            return;
        }
        
        if (null === $request->attributes->get('_route')){
            $pathInfo = $request->getPathInfo();

            // check first if it is /admin/ or /institution/, do nothing if it matches since it should be forwarded to the router
            // this check is necessary if this listner's priority is higher than RouterListener priority to save execution time
//             if ((strpos($pathInfo, '/admin') !== false) || (strpos($pathInfo, '/institution') !== false) || (strpos($pathInfo, '/frontend_search') !== false)) {
//                 return;
//             }
            if ($this->isConfiguredRoute($pathInfo)) {
                return;
            }
            

            $routeObj = null;
            if (is_null($routeObj = $this->routerService->match($pathInfo))) {
                    return;
            }
            
            if($routeObj->getWebsiteSection() == 1) {
                $controller = 'AdminBundle:StaticPage:index';
            }
            else if ($routeObj->getWebsiteSection() == 2) {
                $controller = 'InstitutionBundle:StaticPage:index';
            }
            else {
                $controller = 'FrontendBundle:StaticPage:index';
            }
            $request->attributes->set('_controller', $controller);
        }
    }

    private function isConfiguredRoute($pathInfo)
    {
        if (
            strpos($pathInfo, '/index.html') !== false
        ) {
            return true;
        }

        return false;
    }
}