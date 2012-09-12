<?php 

namespace HealthCareAbroad\InstitutionBundle\Listener;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionInvitationEvent;

use Doctrine\ORM\EntityManager;

class InstitutionInvitationLogListener 
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
	public function onAdd(CreateInstitutionInvitationEvent $event)
	{
		
	}
}