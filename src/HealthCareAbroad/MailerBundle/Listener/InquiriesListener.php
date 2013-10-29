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
            $facilityName = $institutionMedicalCenter->getName() . ' at ' . $institution->getName();
            $urlFacility = $router->generate('frontend_institutionMedicaCenter_profile', array(
                'institutionSlug' => $institution->getSlug(), 'imcSlug' => $institutionMedicalCenter->getSlug()), true
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

    /**
     * FIXME: This is a temporary workaround. The issue is we can't be sure that
     * the account owner's email is legitimate - it can be the autogenerated email
     * we give out at the initial account creation.
     *
     * (Jul 31, 2013) reenabled this
     *
     * (non-PHPdoc)
     * @see \HealthCareAbroad\MailerBundle\Listener\NotificationsListener::isEventProcessable()
     */
    public function isEventProcessable(Event $event)
    {
        return true;

//         $inquiry = $event->getSubject();

//         $to = null;
//         if ($institutionMedicalCenter = $inquiry->getInstitutionMedicalCenter()) {
//             $to = $institutionMedicalCenter->getContactEmail();
//         }

//         if (empty($to)) {
//             $to = $inquiry->getInstitution()->getContactEmail();
//         }

//         return !empty($to);
    }

    public function getTemplateConfigName(Event $event = null)
    {
        return 'notification.inquiries';
    }
}