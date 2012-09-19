<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class NewsEvent extends BaseEvent
{
    public function getNews()
    {
        return $this->getData();
    }
}