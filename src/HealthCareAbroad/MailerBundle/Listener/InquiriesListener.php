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
        $institution = $inquiry->getInstitution();
        $institutionMedicalCenter = $inquiry->getInstitutionMedicalCenter();

        $router = $this->container->get('router');

        if ($institutionMedicalCenter) {
            $facilityName = $institutionMedicalCenter->getName();
            $urlFacility = $router->generate('frontend_institutionMedicaCenter_profile', array(
                            'institutionSlug' => $institution->getSlug(),
                            'imcSlug' => $institutionMedicalCenter->getSlug()),
                            true
            );

            $to = $institutionMedicalCenter->getContactEmail();

        } else {
            $facilityName = $institution->getName();

            switch ($institution->getType()) {
                case InstitutionTypes::SINGLE_CENTER:
                    $routeName = 'frontend_single_center_institution_profile';
                    break;
                case InstitutionTypes::MULTIPLE_CENTER:
                    $routeName = 'frontend_multiple_center_institution_profile';
                    break;
            }

            $urlFacility = $router->generate($routeName, array('institutionSlug' => $institution->getSlug()), true);
        }

        if (empty($to)) {
            if (!$to = $institution->getContactEmail()) {
                $to = $this->container->get('services.institution')->getAccountOwner($institution)->getEmail();
            }
        }

        return array(
            'inquiry' => array(
                'name' => $inquiry->getInquirerName(),
                'email' => $inquiry->getInquirerEmail(),
                'message' => $inquiry->getMessage(),
            ),
            'to' => $to,
            'facility_name' => $facilityName,
            'url' => array(
                'facility' => $urlFacility
            )
        );
    }

    public function getTemplateConfig()
    {
        return 'notification.inquiries';
    }
}