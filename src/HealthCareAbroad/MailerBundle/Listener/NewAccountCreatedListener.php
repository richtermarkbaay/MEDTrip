<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use Symfony\Component\EventDispatcher\Event;

use Symfony\Component\DependencyInjection\ContainerInterface;

class NewAccountCreatedListener
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onNotificationTest(Event $event)
    {
        $subject = $event->getSubject();

        $mailer = $this->container->get('services.mailer.notifications.twig');
        $mailer->sendMessage($subject);
    }
}