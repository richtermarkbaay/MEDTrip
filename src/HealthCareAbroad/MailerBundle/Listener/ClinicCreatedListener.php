<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use Symfony\Component\EventDispatcher\GenericEvent;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ClinicCreatedListener extends NotificationsListener
{
    public function getData(Event $event)
    {
        $institutionMedicalCenter = $event->getSubject();
        $institution = $institutionMedicalCenter->getInstitution();

        $router = $this->container->get('router');
        $urlCenter = $router->generate('institution_medicalCenter_view', array('imcId' => $institutionMedicalCenter->getId()), true);

        $to = $event->getArgument('userEmail');

        $inquiriesEmail = $institutionMedicalCenter->getContactEmail();
        if (empty($inquiriesEmail)) {
            if (!$inquiriesEmail = $institution->getContactEmail()) {
                $inquiriesEmail = $this->container->get('services.institution')->getAccountOwner($institution)->getEmail();
            }
        }

        $data = array(
            'clinic_name' => $institutionMedicalCenter->getName(),
            'institution_name' => $institution->getName(),
            'to' => $to,
            'email' => array(
                'inquiries' => $inquiriesEmail
            ),
            'url' => array(
                'center' => $urlCenter,
                'add_centers' => $router->generate('institution_medicalCenter_index', array(), true),
                'center_gallery' => $router->generate('institution_mediaGallery_index', array(), true),
                'center_treatments' => $urlCenter.'#specializations',
                'center_doctors' => $urlCenter.'#doctors',
                'contact_info' => $urlCenter.'#contact-details'
            )
        );

        $accountOwner = $this->container->get('services.institution')->getAccountOwner($institution);

        if (strtolower($to) != strtolower($accountOwner->getEmail())) {
            $data['cc'] = $accountOwnerEmail;
        }

        return $data;
    }

    public function isEventProcessable(Event $event)
    {
        $institutionMedicalCenter = $event->getSubject();

        return InstitutionTypes::MULTIPLE_CENTER == $institutionMedicalCenter->getInstitution()->getType();
    }

    public function getTemplateConfig(Event $event = null) {
        return 'notification.clinic_created';
    }
}