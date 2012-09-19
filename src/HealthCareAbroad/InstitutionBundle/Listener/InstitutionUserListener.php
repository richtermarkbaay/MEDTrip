<?php
namespace HealthCareAbroad\InstitutionBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\DependencyInjection\ContainerInterface;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionUserEvent;

/**
 * Listener for institution bundle of institution user related events
 * 
 * @author Allejo Chris G. Velarde
 */
class InstitutionUserListener
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->doctrine = $this->container->get('doctrine');
    }
    
    /**
     * Listener for event.institution_user.add event. Will delete used invitation
     * 
     * @param CreateInstitutionUserEvent $event
     */
    public function onAdd(CreateInstitutionUserEvent $event)
    {
        if ($invitation = $event->getUsedInvitation()) {
            $em = $this->doctrine->getEntityManager();
            $em->remove($invitation);
            $em->flush();
        }
    }
}