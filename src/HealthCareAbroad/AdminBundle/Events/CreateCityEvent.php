<?php 
namespace HealthCareAbroad\AdminBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use HealthCareAbroad\HelperBundle\Entity\City;

class CreateCityEvent extends Event
{
	public $city;
	
	public function __construct(City $city)
	{
		$this->city = $city;
	}		
	
	public function getCity()
	{
		return $this->city;
	}
}