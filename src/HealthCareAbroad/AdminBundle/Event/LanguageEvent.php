<?php
namespace HealthCareAbroad\AdminBundle\Event;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class LanguageEvent extends BaseEvent
{
    public function getLanguage()
    {
        return $this->getData();
    }
}