<?php 

namespace HealthCareAbroad\InstitutionBundle\Listener;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionUserInvitationEvent;

use Doctrine\ORM\EntityManager;

class InstitutionUserInvitationLogListener 
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
	public function onAdd(CreateInstitutionUserInvitationEvent $event)
	{
		
	}
}