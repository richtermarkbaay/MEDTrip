<?php

namespace HealthCareAbroad\AdminBundle\Listener;

use HealthCareAbroad\AdminBundle\Events\CreateNewsEvent;
use Doctrine\ORM\EntityManager;

class NewsLogListener
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

	public function onAdd(CreateNewsEvent $event)
	{

	}

	public function onEdit(CreateNewsEvent $event)
	{

	}

	public function onDelete(CreateNewsEvent $event)
	{

	}
}