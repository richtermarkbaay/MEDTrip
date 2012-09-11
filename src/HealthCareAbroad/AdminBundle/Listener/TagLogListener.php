<?php 

namespace HealthCareAbroad\AdminBundle\Listener;

use HealthCareAbroad\AdminBundle\Events\CreateTagEvent;

use Doctrine\ORM\EntityManager;

class TagLogListener 
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
	
	public function onAdd(CreateTagEvent $event)
	{
		
	}
	public function onEdit(CreateTagEvent $event)
	{
	
	}
	public function onDelete(CreateTagEvent $event)
	{
	
	}
}
