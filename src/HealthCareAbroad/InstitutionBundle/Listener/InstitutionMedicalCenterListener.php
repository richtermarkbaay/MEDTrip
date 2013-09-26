<?php

namespace HealthCareAbroad\InstitutionBundle\Listener;

use HealthCareAbroad\HelperBundle\Services\RecentlyApprovedListingService;

use Symfony\Component\EventDispatcher\GenericEvent;

use Doctrine\ORM\EntityManager;

class InstitutionMedicalCenterListener
{
    /**
     * 
     * @var Doctrine\ORM\EntityManager
     */
    private $em;
    
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onDelete(){
    	 
    }
    
    public function onEdit(){
    	
    }

    public function onUpdateStatus(GenericEvent $event){

        // Update recentlyApprovedListings
        $recentlyApprovedListingService = new RecentlyApprovedListingService();
        $recentlyApprovedListingService->setEntityManager($this->em);
        $recentlyApprovedListingService->updateInstitutionMedicalCenterListing($event->getSubject());
        // End of Update recentlyApprovedListing
    }
}