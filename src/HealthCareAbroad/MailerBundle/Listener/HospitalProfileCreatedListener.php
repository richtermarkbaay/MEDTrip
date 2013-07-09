<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use HealthCareAbroad\UserBundle\Entity\Institution;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HospitalProfileCreatedListener extends NotificationsListener
{
    /**
     * FIXME: $to key should contain the account owner of the institution. Right
     * now, we don't have an "account owner" type of user. We will use doctrine's
     * array collection's first() method - assumption here is the system will
     * always create upon account creation a user with admin role, of which we
     * are depending that the array collection first() will return
     *
     * @see \HealthCareAbroad\MailerBundle\Listener\NotificationsListener::getData()
     */
    public function getData(Event $event)
    {
        $institution = $event->getSubject();

        $router = $this->container->get('router');

        $urlDefaultCenter = $router->generate('institution_medicalCenter_index', array(), true);
        $urlDefaultCenterGallery = $router->generate('institution_mediaGallery_index', array(), true);
        $institutionType = $institution->getType();
        switch ($institutionType) {
            case InstitutionTypes::SINGLE_CENTER:
                $urlTreatmentList = $urlProfle.'#specializations';
                break;
            case InstitutionTypes::MULTIPLE_CENTER:
                $urlTreatmentList = $urlDefaultCenter;
                break;
            default:
                $urlTreatmentList = '';
        }
        switch ($institutionType) {
            case InstitutionTypes::SINGLE_CENTER:
                $urlFeatureDoctors = $urlProfle.'#doctors';
                break;
            case InstitutionTypes::MULTIPLE_CENTER:
                $urlFeatureDoctors = $urlDefaultCenter;
                break;
            default:
                $urlFeatureDoctors = '';
        }
        $urlContactDetails = $urlDefaultCenter.'#contact-details';

        return array(
            'institution_name' => $institution->getName(),
            'to' => $this->getAccountOwner($institution)->getEmail(),
            'url' => array(
                'add_centers' => $urlDefaultCenter,
                'upload_photos' => $urlDefaultCenterGallery,
                'list_treatments' => $urlTreatmentList,
                'feature_doctors' => $urlFeatureDoctors,
                'contact_info' => $urlContactDetails
            )
        );
    }

    public function getTemplateConfig()
    {
        return 'notification.hospital_profile_created';
    }

    private function getAccountOwner($institution)
    {
        $userService = $this->container->get('services.institution_user');
        $hydratedUser = $userService->getAccountData($institution->getInstitutionUsers()->first());

        return $hydratedUser;
    }
}
