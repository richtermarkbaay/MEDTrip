<?php

namespace HealthCareAbroad\InstitutionBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionEvent;	
use HealthCareAbroad\UserBundle\Entity\InstitutionUserType;	
use HealthCareAbroad\InstitutionBundle\Entity\Institution;


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
    
    public function onCreate(InstitutionEvent $event)
    {
    	//get institution
    	$institution = $event->getInstitution();
        
    	//persist data to create institutionUserTypes
    	$institutionUserType = new InstitutionUserType();
    	$institutionUserType->setInstitution($institution);
    	$institutionUserType->setName(Institution::USER_TYPE);
    	$institutionUserType->setStatus(3);
    	$this->em->persist($institutionUserType);
    	$this->em->flush();
    	return $institutionUserType;
    }
    
}