<?php 

namespace HealthCareAbroad\AdminBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\AdminBundle\Events\CreateMedicalCenterEvent;

class MedicalCenterLogListener
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

	public function onDelete(CreateMedicalCenterEvent $event){

	}

	public function onEdit(CreateMedicalCenterEvent $event){
		 
	}

	public function onAdd(CreateMedicalCenterEvent $event)
	{
		 
	}
	 

}