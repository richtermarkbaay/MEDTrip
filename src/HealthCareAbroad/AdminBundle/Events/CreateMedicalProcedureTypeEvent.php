<?php 
namespace HealthCareAbroad\AdminBundle\Events;

use Symfony\Component\EventDispatcher\Event;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedureType;

class CreateMedicalProcedureTypeEvent extends Event
{
	protected $procedureType;
	
	public function __construct(MedicalProcedureType $procedureType)
	{
		$this->procedureType = $procedureType;
	}
	
	public function getMedicalProcedureType()
	{
		return $this->procedureType;
	}
}