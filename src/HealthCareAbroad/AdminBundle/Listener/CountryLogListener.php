<?php 

namespace HealthCareAbroad\AdminBundle\Listener;

use HealthCareAbroad\AdminBundle\Events\CreateCountryEvent;
use Doctrine\ORM\EntityManager;

class CountryLogListener 
{
	/*
	 * 
	 * @var Doctrine\ORM\EntityManager 
	 */
	
	private $em;
	
	public function setEntityManager(EntityManager $em)
	{
		$this->em = $em;
	}
	
	public function onAdd(CreateCountryEvent $event)
	{
		
	}
	
	public function onEdit(CreateCountryEvent $event)
	{
	
	}
	
	public function onDelete(CreateCountryEvent $event)
	{
	
	}
}