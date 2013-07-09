<?php
namespace HealthCareAbroad\MailerBundle\Listener;

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

        $urlCenter = $this->container->get('router')->generate('institution_medicalCenter_view', array('imcId' => $institutionMedicalCenter->getId()), true);

        return array(
            'to' => $institutionMedicalCenter->getInstitution()->getInstitutionUsers()->getEmail(),
            'url' => array(
                'center' => $urlCenter,
                'center_gallery' => $router->generate('institution_mediaGallery_index', array(), true),
                'center_treatments' => $urlCenter.'#specializations',
                'center_doctors' => $urlCenter.'#doctors',
                'contact_info' => $urlCenter.'#contact-details'
            )
        );
    }

    public function getTemplateConfig()
    {
        return 'notification.new_listings_approved';
    }
}