<?php
namespace HealthCareAbroad\AdminBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use HealthCareAbroad\HelperBundle\Entity\News;

class CreateNewsEvent extends Event
{
	public $news;

	public function __construct(News $news)
	{
		$this->news = $news;
	}

	public function getNews()
	{
		return $this->news;
	}
}