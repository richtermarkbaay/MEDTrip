<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Symfony\Component\EventDispatcher\GenericEvent;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NewListingsApprovedListener extends NotificationsListener
{
    public function getData(Event $event)
    {
        $institutionMedicalCenter = $event->getSubject();

        $router = $this->container->get('router');

        $urlCenter = $router->generate('institution_medicalCenter_view', array('imcId' => $institutionMedicalCenter->getId()), true);

        $accountOwner = $this->container->get('services.institution')->getAccountOwner($institutionMedicalCenter->getInstitution());

        return array(
            'to' => $accountOwner->getEmail(),
            'url' => array(
                'center' => $urlCenter,
                'add_centers' => $router->generate('institution_medicalCenter_index', array(), true),
                'center_gallery' => $router->generate('institution_mediaGallery_index', array(), true),
                'center_treatments' => $urlCenter.'#specializations',
                'center_doctors' => $urlCenter.'#doctors',
                'contact_info' => $urlCenter.'#contact-details'
            )
        );
    }

    public function isEventProcessable(Event $event)
    {
        $institutionMedicalCenter = $event->getSubject();

        return InstitutionMedicalCenterStatus::APPROVED == $institutionMedicalCenter->getStatus();
    }

    public function getTemplateConfigName(Event $event = null)
    {
        return 'notification.new_listings_approved';
    }

    public function propagateExceptions(Event $event)
    {
        //silently ignore for now
        return false;
    }
}