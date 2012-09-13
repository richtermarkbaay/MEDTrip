<?php

namespace HealthCareAbroad\InstitutionBundle\Listener;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvent;

use HealthCareAbroad\UserBundle\Entity\InstitutionUserRole;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\EditInstitutionEvent;
use HealthCareAbroad\InstitutionBundle\Event\DeleteInstitutionEvent;
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
    	$institutionUserType->setName(Institution::USER_TYPE);
    	$institutionUserType->setStatus(3);
    	
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