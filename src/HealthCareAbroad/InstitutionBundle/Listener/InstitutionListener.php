<?php

namespace HealthCareAbroad\InstitutionBundle\Listener;

use HealthCareAbroad\HelperBundle\Services\RecentlyApprovedListingService;

use Symfony\Component\EventDispatcher\GenericEvent;

use HealthCareAbroad\UserBundle\Entity\InstitutionUserTypeStatuses;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvent;

use HealthCareAbroad\UserBundle\Entity\InstitutionUserRole;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\UserBundle\Entity\InstitutionUserType;	
use HealthCareAbroad\UserBundle\Entity\InstitutionUser;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\UserBundle\Services\InstitutionUserService;



class InstitutionListener
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
    
    /**
     * @var HealthCareAbroad\UserBundle\Services\InstitutionUserService
     */
    protected $institutionUserService;
    
    public function setInstitutionUserService(InstitutionUserService $institutionUserService)
    {
    	$this->institutionUserService = $institutionUserService;
    }
    
    public function onDelete(){
    	 
    }
    
    public function onEdit(){
    	
    }

    public function onUpdateStatus(GenericEvent $event){

        // Update recentlyApprovedListings
        $recentlyApprovedListingService = new RecentlyApprovedListingService();
        $recentlyApprovedListingService->setEntityManager($this->em);
        $recentlyApprovedListingService->updateInstitutionListing($event->getSubject());
        // End of Update recentlyApprovedListing
    }

    public function onAdd(CreateInstitutionEvent $event)
    {
        $institution = $event->getInstitution();
    	$institutionUser = $event->getInstitutionUser();
    	
    	if (!$institution instanceof Institution || !$institutionUser instanceof InstitutionUser) {
    	    throw new \Exception("{$event->getName()} handled by ".__CLASS__."::onAdd listener has invalid data.");
    	}
    	
    	//persist data to create institutionUserTypes
    	$institutionUserType = new InstitutionUserType();
    	$institutionUserType->setInstitution($institution);
    	$institutionUserType->setName('ADMIN');
    	$institutionUserType->setStatus(InstitutionUserTypeStatuses::getBitValueForBuiltInUserType());
    	
    	// add role to this first user type as super admin for this institution
    	$adminInstitutionRole = $this->em->getRepository('UserBundle:InstitutionUserRole')->findOneBy(array('name' => InstitutionUserRole::SUPER_ADMIN));
    	if ($adminInstitutionRole) {
    	    $institutionUserType->addInstitutionUserRole($adminInstitutionRole);
    	}
    	
    	$this->em->persist($institutionUserType);
    	$this->em->flush();
    	
	    //create institutionUser account and global account
	    $this->createInstitutionUser($institutionUserType, $institutionUser);
    }
    
    public function createInstitutionUser(InstitutionUserType $institutionUserType, InstitutionUser $institutionUser)
    {
    	$institutionUser->setInstitutionUserType($institutionUserType);
    	$institutionUser = $this->institutionUserService->create($institutionUser);
    }
    
}