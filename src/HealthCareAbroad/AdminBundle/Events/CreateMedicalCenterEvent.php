<?php 
namespace HealthCareAbroad\AdminBundle\Events;

use Symfony\Component\EventDispatcher\Event;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

class CreateMedicalCenterEvent extends Event
{
	protected $medicalCenters;
	
	public function __construct(MedicalCenter $centers)
	{
		$this->medicalCenters = $centers;
	}
	
	public function getMedicalCenters()
	{
		return $this->medicalCenters;
	}
}