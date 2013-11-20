<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use HealthCareAbroad\MailerBundle\Event\MailerBundleEvents;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PasswordListener extends NotificationsListener
{
    public function getData(Event $event)
    {
        $subject = $event->getSubject();

        switch ($event->getName()) {
            case MailerBundleEvents::NOTIFICATIONS_PASSWORD_RESET:

                return array(
                    'to' => $subject['email'],
                    'email' => array(
                        'hca_account' => $subject['email']
                    ),
                    'url' => array(
                        'password_reset' => $this->container->get('router')->generate('institution_set_new_password', array('token' => $subject['token']), true)
                    ),
                    'link_expiration' => $subject['expiresIn'] . ' days'
                );

            case MailerBundleEvents::NOTIFICATIONS_PASSWORD_CONFIRM:

                return array('to' => $subject['email']);

            default:
                throw new \Exception('Unsupported event.');
        }
    }

    public function getTemplateConfigName(Event $event = null)
    {
        switch ($event->getName()) {
            case MailerBundleEvents::NOTIFICATIONS_PASSWORD_RESET:
                return 'notification.password_reset';
            case MailerBundleEvents::NOTIFICATIONS_PASSWORD_CONFIRM:
                return 'notification.password_confirm';
            default:
                throw new \Exception('Unsupported event.');
        }
    }
}