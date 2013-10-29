<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AccountCreatedListener extends NotificationsListener
{
    public function getData(Event $event)
    {
        $institutionUser = $event->getInstitutionUser();
        $router = $this->container->get('router');

        $urlProfile = $router->generate('institution_account_profile', array(), true);
        $urlDefaultCenter = $router->generate('institution_medicalCenter_index', array(), true);
        $urlDefaultCenterGallery = $router->generate('institution_mediaGallery_index', array(), true);
        $institutionType = $institutionUser->getInstitution()->getType();
        switch ($institutionType) {
            case InstitutionTypes::SINGLE_CENTER:
                $urlTreatmentList = $urlProfile.'#specializations';
                break;
            case InstitutionTypes::MULTIPLE_CENTER:
                $urlTreatmentList = $urlDefaultCenter;
                break;
            default:
                $urlTreatmentList = '';
        }
        switch ($institutionType) {
            case InstitutionTypes::SINGLE_CENTER:
                $urlFeatureDoctors = $urlProfile.'#doctors';
                break;
            case InstitutionTypes::MULTIPLE_CENTER:
                $urlFeatureDoctors = $urlDefaultCenter;
                break;
            default:
                $urlFeatureDoctors = '';
        }
        $urlContactDetails = $urlProfile.'#contact-details';

        return array(
            'to' => $institutionUser->getEmail(),
            'email' => array(
                'user_login' => $institutionUser->getEmail()
            ),
            'url' => array(
                'complete_profile' => $urlProfile,
                'add_centers' => $urlDefaultCenter,
                'upload_photos' => $urlDefaultCenterGallery,
                'list_treatments' => $urlTreatmentList,
                'feature_doctors' => $urlFeatureDoctors,
                'contact_info' => $urlContactDetails
            )
        );
    }

    public function getTemplateConfigName(Event $event = null)
    {
        return 'notification.account_created';
    }

    public function propagateExceptions(Event $event)
    {
        //silently ignore for now
        return false;
    }
}