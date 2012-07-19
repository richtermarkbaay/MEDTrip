<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use HealthCareAbroad\UserBundle\Event\CreateProviderUserEvent;

class ProviderUserListener
{
    public function onCreate(CreateProviderUserEvent $event)
    {
        //TODO: add a mail to sending queue
    }
}