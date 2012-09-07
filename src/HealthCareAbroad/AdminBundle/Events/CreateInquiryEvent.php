<?php 

namespace HealthCareAbroad\AdminBundle\Events;

use HealthCareAbroad\AdminBundle\Entity\Inquiry;
use Symfony\Component\EventDispatcher\Event;

class CreateInquiryEvent extends Event
{
	public $query;
	
	public function __construct(Inquiry $query)
	{
		$this->query = $query;
	}
	
	public function getInquiry()
	{
		return $this->query;
	}
}

