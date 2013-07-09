<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InquiriesListener extends NotificationsListener
{
    public function getData(Event $event)
    {
        $inquiry = $event->getSubject();
        $institution = $event->getArgument('institution');
        $router = $this->container->get('router');

        $message = <<< EOT
{$inquiry->getInquirySubject()->getName()}
{$inquiry->getMessage()}
EOT;
        return array(
            'inquiry' => array(
                'name' => $inquiry->getFirstName().' '.$inquiry->getLastName(),
                'email' => $inquiry->getEmail(),
                'message' => $message,
            ),
            'to' => 'inquiry@healthcareabroad.com',
            'institution_name' => $institution->getName(),
            'url' => array(
                'institution' => $router->generate('institution_account_profile', array(), true)
            )
        );
    }

    public function getTemplateConfig()
    {
        return 'notification.inquiries';
    }
}