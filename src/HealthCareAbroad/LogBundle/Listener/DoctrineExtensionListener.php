<?php
/**
 * Doctrine extension listener
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\LogBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class DoctrineExtensionListener implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * This handler will be used by Loggable doctrine extension to set the username
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $securityContext = $this->container->get('security.context', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        if (null != $securityContext && null != $securityContext->getToken()) {
            // there is a logged in user
            $loggable = $this->container->get('gedmo.listener.loggable');
            $loggable->setUsername((string)$this->container->get('session')->get('accountId', null));
        }
    }
}