<?php 
namespace HealthCareAbroad\AdminBundle\Events;

use Symfony\Component\EventDispatcher\Event;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalProcedure;

class CreateMedicalProcedureEvent extends Event
{
	protected $procedure;
	
	public function __construct(MedicalProcedure $procedure)
	{
		$this->procedure = $procedure;
	}
	
	public function getMedicalProcedure()
	{
		return $this->procedure;
	}
}