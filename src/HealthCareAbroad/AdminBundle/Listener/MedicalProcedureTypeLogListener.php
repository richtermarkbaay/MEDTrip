<?php 

namespace HealthCareAbroad\AdminBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\AdminBundle\Events\CreateTreatmentEvent;

class TreatmentLogListener
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

	public function onDelete(CreateTreatmentEvent $event){

	}

	public function onEdit(CreateTreatmentEvent $event){
		 
	}

	public function onAdd(CreateTreatmentEvent $event)
	{
		 
	}
	 

}