<?php 

/**
 * @author Adelbert D. Silla
 */

namespace HealthCareAbroad\HelperBundle\Listener;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class PreExecuteController
{
	/**
	 * kernel.controller listener method
	 *
	 * @param FilterControllerEvent $event
	 */
	public function onKernelController(FilterControllerEvent $event)
	{
	    // do this filter only if there is a matched route
	    if (null === $event->getRequest()->attributes->get('_route')) {
	        
	        return;
	    }
	    
	    if(HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {

	        $controllers = $event->getController();
	        
	        if (isset($controllers[0])){
	            $controller = $controllers[0];

	            if(method_exists($controller, 'preExecute')){
	                $controller->preExecute();
	            }
	        }
	    }
	}
}