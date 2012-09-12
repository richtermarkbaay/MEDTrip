<?php

namespace HealthCareAbroad\InstitutionBundle\Listener;

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
    
    public function onDelete(CreateInstitutionEvent $event){
    	 
    }
    
    public function onEdit(CreateInstitutionEvent $event){
    	
    }
    
    public function onAdd(CreateInstitutionEvent $event)
    {
    	//get institution
    	$institution = $event->getInstitution();
    	
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
    	
    	//get institutionUser
    	$institutionUser = $event->getInstitutionUser();
    	
    	//create institutionUser account and global account
    	$institutionUser = $this->createInstitutionUser($institutionUserType, $institutionUser);
    	return $institutionUser;
    }
    
    public function createInstitutionUser(InstitutionUserType $institutionUserType, InstitutionUser $institutionUser)
    {
    	$institutionUser->setInstitutionUserType($institutionUserType);
    	$institutionUser = $this->institutionUserService->create($institutionUser);
    	return $institutionUser;
    }
    
}