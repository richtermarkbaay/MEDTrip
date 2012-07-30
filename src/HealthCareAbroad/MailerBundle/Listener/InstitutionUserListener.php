<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use HealthCareAbroad\UserBundle\Event\CreateInstitutionUserEvent;

class InstitutionUserListener
{
    public function onCreate(CreateInstitutionUserEvent $event)
    {
        //TODO: add a mail to sending queue
    }
}