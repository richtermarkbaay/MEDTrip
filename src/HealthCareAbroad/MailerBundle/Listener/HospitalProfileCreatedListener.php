<?php
namespace HealthCareAbroad\MailerBundle\Listener;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;
use HealthCareAbroad\UserBundle\Entity\Institution;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

class HospitalProfileCreatedListener extends NotificationsListener
{
    /**
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
                $urlTreatmentList = $urlDefaultCenter.'#specializations';
                break;
            case InstitutionTypes::MULTIPLE_CENTER:
                $urlTreatmentList = $urlDefaultCenter;
                break;
            default:
                $urlTreatmentList = '';
        }
        switch ($institutionType) {
            case InstitutionTypes::SINGLE_CENTER:
                $urlFeatureDoctors = $urlDefaultCenter.'#doctors';
                break;
            case InstitutionTypes::MULTIPLE_CENTER:
                $urlFeatureDoctors = $urlDefaultCenter;
                break;
            default:
                $urlFeatureDoctors = '';
        }
        $urlContactDetails = $urlDefaultCenter.'#contact-details';

        $accountOwnerEmail = $this->container->get('services.institution')->getAccountOwner($institution)->getEmail();

        return array(
            'institution_name' => $institution->getName(),
            'to' => $accountOwnerEmail,
            'url' => array(
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
        return 'notification.hospital_profile_created';
    }

    public function propagateExceptions(Event $event)
    {
        //silently ignore for now
        return false;
    }
}
