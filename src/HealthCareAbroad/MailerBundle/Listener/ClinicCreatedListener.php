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
        $data = array(
            'clinic_name' => $institutionMedicalCenter->getName(),
            'institution_name' => $institution->getName(),
            'to' => $to,
            'url' => array(
                'center' => $urlCenter,
                'center_gallery' => $router->generate('institution_mediaGallery_index', array(), true),
                'center_treatments' => $urlCenter.'#specializations',
                'center_doctors' => $urlCenter.'#doctors',
                'contact_info' => $urlCenter.'#contact-info'
            )
        );

        if ($to != $this->getAccountOwner($institution)->getEmail()) {
            $data['cc'] = $accountOwnerEmail;
        }

        return $data;
    }

    public function isEventProcessable(Event $event)
    {
        $institutionMedicalCenter = $event->getSubject();

        return InstitutionTypes::MULTIPLE_CENTER == $institutionMedicalCenter->getInstitution()->getType();
    }

    public function getTemplateConfig() {
        return 'notification.clinic_created';
    }

    private function getAccountOwner($institution)
    {
        $userService = $this->container->get('services.institution_user');
        $hydratedUser = $userService->getAccountData($institution->getInstitutionUsers()->first());

        return $hydratedUser;
    }
}