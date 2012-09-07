<?php 

namespace HealthCareAbroad\AdminBundle\Listener;

use HealthCareAbroad\AdminBundle\Events\CreateInquiryEvent;
use Doctrine\ORM\EntityManager;

class InquiryLogListener
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
	
	public function onAdd(CreateInquiryEvent $event){
		
	}
	
	public function onEdit(CreateInquiryEvent $event){
	
	}
	
	public function onDelete(CreateInquiryEvent $event){
	
	}
}
