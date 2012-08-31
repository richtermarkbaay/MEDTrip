<?php
/**
 * Before controller listener for controllers in the /institution routes that needs an institution instance
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\InstitutionBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\InstitutionBundle\Controller\InstitutionAwareController;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class InstitutionAwareBeforeControllerListener
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $matchedRoute = $request->get('_route');
        
        if (!\preg_match('/^institution/', $matchedRoute)) {
            // not a client admin route
            return false;
        }
        
        $callable = $event->getController();
        
        if (!\is_array($callable)) {
            // non-object controller
            return;
        }
        $controllerObj = $callable[0];
        
        if ($controllerObj instanceof InstitutionAwareController) {
            $institutionId = $request->getSession()->get('institutionId', 0);
            if ($institutionId) {
                $institution = $this->doctrine->getRepository('InstitutionBundle:Institution')->find($institutionId);
                if (!$institution) {
                    $controllerObj->throwInvalidInstitutionException();
                }
                
                $controllerObj->setInstitution($institution);
            }
        }
    }
}