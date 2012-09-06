<?php 

namespace HealthCareAbroad\AdminBundle\Listener;

use Doctrine\ORM\EntityManager;

use HealthCareAbroad\AdminBundle\Events\CreateMedicalProcedureTypeEvent;

class MedicalProcedureTypeLogListener
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

	public function onDelete(CreateMedicalProcedureTypeEvent $event){

	}

	public function onEdit(CreateMedicalProcedureTypeEvent $event){
		 
	}

	public function onAdd(CreateMedicalProcedureTypeEvent $event)
	{
		 
	}
	 

}