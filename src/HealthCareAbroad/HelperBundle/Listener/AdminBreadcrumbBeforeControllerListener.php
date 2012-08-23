<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\HelperBundle\Listener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class AdminBreadcrumbBeforeControllerListener
{
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
        
        
    }    
}