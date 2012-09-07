<?php


namespace HealthCareAbroad\AdminBundle\Events;

use HealthCareAbroad\HelperBundle\Entity\Country;
use Symfony\Component\EventDispatcher\Event;

class CreateCountryEvent extends Event
{
	public $country;
	public function __construct(Country $country)
	{
		$this->country = $country;
	}
	
	public function getCountry()
	{
		$this->country = $country;
	}
}