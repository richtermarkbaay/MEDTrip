<?php 

namespace HealthCareAbroad\AdminBundle\Listener;

use HealthCareAbroad\AdminBundle\Events\CreateCityEvent;
use Doctrine\ORM\EntityManager;

class CityLogListener 
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
	
	public function onAdd(CreateCityEvent $event)
	{
		
	}
	
	public function onEdit(CreateCityEvent $event)
	{
	
	}
	
	public function onDelete(CreateCityEvent $event)
	{
	
	}
}