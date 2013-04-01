<?php
namespace HealthCareAbroad\FrontendBundle\Listener;

use Symfony\Bundle\FrameworkBundle\HttpKernel;

use HealthCareAbroad\FrontendBundle\Services\FrontendRouteService;

use Symfony\Component\HttpKernel\Exception\FlattenException;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class DefaultRouterListener
{
    /**
     * @var FrontendRouteService
     */
    private $routerService;

    public function setRouterService(FrontendRouteService $service)
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
                if (is_null($routeObj = $this->routerService->addRoute($pathInfo))) {
                    return;
                }
            }
            

            $controller = $routeObj->getController() ? $routeObj->getController() : 'FrontendBundle:Default:commonLanding';
            $variables = \json_decode($routeObj->getVariables(), true);
            $request->attributes->set('_controller', $controller);
            $request->attributes->set('_route_params', $variables);

            // TODO - This route does not exists! should be change when error occur!
            // Added by: Adelbert Silla
            // Being used in breadcrumbs for combined search.
            $request->attributes->set('_route', FrontendRouteService::COMBINED_SEARCH_ROUTE_NAME);
        }
    }

    private function isConfiguredRoute($pathInfo)
    {
        if (
            strpos($pathInfo, '/index.html') !== false ||
            strpos($pathInfo, '/search') !== false ||
            strpos($pathInfo, '/hospital') !== false ||
            strpos($pathInfo, '/related-search') !== false ||
            strpos($pathInfo, '/statistics') !== false ||
            strpos($pathInfo, '/destination') !== false ||
            //strpos($pathInfo, '/treatment') !== false ||
            strpos($pathInfo, '/admin') !== false ||
            strpos($pathInfo, '/institution') !== false
        ) {
            return true;
        }

        return false;
    }
}