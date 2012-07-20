<?php
/**
 * Common listener for provider user events
 * 
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\UserBundle\Listener;

use HealthCareAbroad\UserBundle\Event\CreateProviderUserEvent;

class ProviderUserListener
{
    /**
     * 
     * @var Doctrine\ORM\EntityManager
     */
    private $em;
    
    public function setEntityManager(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function onCreate(CreateProviderUserEvent $event)
    {
        if ($invitation = $event->getUsedInvitation()) {
            // delete the used invitation
            $this->em->remove($invitation);
            $this->em->flush();
        }
    }
    
}