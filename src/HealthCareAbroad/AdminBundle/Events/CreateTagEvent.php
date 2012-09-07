<?php 

namespace HealthCareAbroad\AdminBundle\Events;

use HealthCareAbroad\HelperBundle\Entity\Tag;
use Symfony\Component\EventDispatcher\Event;

class CreateTagEvent extends Event
{
	
	public $tag;
	
	public function __construct(Tag $tag)
	{
		$this->tag = $tag;
	}
	
	public function getTag()
	{
		return $this->tag;
	}
}

