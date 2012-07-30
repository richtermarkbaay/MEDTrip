<?php
/**
 * Common listener for institution user events
 * 
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\UserBundle\Listener;

use HealthCareAbroad\UserBundle\Event\CreateInstitutionUserEvent;

class InstitutionUserListener
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
    
    public function onCreate(CreateInstitutionUserEvent $event)
    {
        if ($invitation = $event->getUsedInvitation()) {
            // delete the used invitation
            $this->em->remove($invitation);
            $this->em->flush();
        }
    }
    
}