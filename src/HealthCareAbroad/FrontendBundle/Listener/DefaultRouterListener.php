<?php
namespace HealthCareAbroad\FrontendBundle\Listener;

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

        if (null === $request->attributes->get('_route')){

            $pathInfo = $request->getPathInfo();

            // check first if it is /admin/ or /institution/, do nothing if it matches since it should be forwarded to the router
            // this check is necessary if this listner's priority is higher than RouterListener priority to save execution time
            if (\preg_match('/^\/admin\//', $pathInfo) || \preg_match('/^\/institution\//', $pathInfo)) {

                return;
            }

            $routeObj = $this->routerService->match($request->getPathInfo());

            if (!$routeObj) {
                $routeObj = $this->routerService->addRoute($request->getPathInfo());
            }

            // modify attributes only if we get a valid route object
            if ($routeObj) {
                $controller = 'FrontendBundle:Default:commonLanding';
                $variables = \json_decode($routeObj->getVariables(), true);
                $request->attributes->set('_controller', $controller);
                $request->attributes->set('_route_params', $variables);
            }
        }
    }
}