<?php
/**
 * Institution Alert Listener
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\HelperBundle\Listener\Alerts;

use HealthCareAbroad\HelperBundle\Services\AlertRecipient;

use HealthCareAbroad\LogBundle\Exception\ListenerException;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;
use HealthCareAbroad\HelperBundle\Services\AlertService;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroupStatus;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstitutionAlertListener extends BaseAlertListener
{
    protected $medicalCenterClassName;


    public function __construct(ContainerInterface $container)
    {        
        parent::__construct($container);

        $this->medicalCenterClassName = AlertClasses::getClassName(AlertClasses::INSTITUTION_MEDICAL_CENTER);
    }

    /**
     * 
     * @param BaseEvent $event
     */
    public function onAddMedicalCenterAction(BaseEvent $event)
    {
        $draftAlert = $this->createDraftListingAlert($event->getData(), $event->getOptions());

        $this->alertService->save($draftAlert);
    }

    
    public function onEditMedicalCenterAction(BaseEvent $event)
    {
        if($event->getOption('previousStatus') == InstitutionMedicalCenterGroupStatus::DRAFT) {
            $alertData = array();
            $object = $event->getData();

            // REMOVE Draft Alert if exists!
            if($draftAlert = $this->createRemoveDraftAlert($object)) {
                array_push($alertData, $draftAlert);                    
            }

            if($object->getStatus() == InstitutionMedicalCenterGroupStatus::PENDING) {
                $pendingAlert = $this->createPendingListingAlert($object, $event->getOptions());
                array_push($alertData, $pendingAlert);
            }

            $this->alertService->multipleUpdate($alertData);
        }
    }

    /**
     *
     * @param BaseEvent $event
     */
    public function onUpdateMedicalCenterStatusAction(BaseEvent $event)
    {
        $alertData = array();
        $object = $event->getData();

        if(get_class($object) != $this->medicalCenterClassName) {
            throw new ListenerException('Invalid class given ' . get_class($object) . '. Required class is '. $this->medicalCenterClassName);
        }

        switch($object->getStatus()) {
            case InstitutionMedicalCenterGroupStatus::PENDING :

                // ADD Pending Listing Alert
                $pendingAlert = $this->createPendingListingAlert($object, $event->getOptions());
                array_push($alertData, $pendingAlert);

                // REMOVE Draft Listing Alert if exists!
                if($draftAlert = $this->createRemoveDraftAlert($object)) {
                    array_push($alertData, $draftAlert);                    
                }

                break;

            case InstitutionMedicalCenterGroupStatus::APPROVED :
                $x = $event->getOptions();

                // Add Approved Listing Alert
                if($approvedAlert = $this->createApprovedListingAlert($object, $event->getOptions())); {
                    array_push($alertData, $approvedAlert);                    
                }

                // ADD Expired Listing Alert
                if($expiredAlert = $this->createExpiredListingAlerts($object, $event->getOptions())) {
                    $alertData = array_merge($alertData, $expiredAlert);
                }

                // REMOVE Pending Listing Alert if exists!
                if($draftAlert = $this->createRemovePendingAlert($object, $event->getOptions())) {
                    array_push($alertData, $draftAlert);                    
                }

                break;
        }

        $this->alertService->multipleUpdate($alertData);
    }
    
    /**
     *
     * @param BaseEvent $event
     */
    public function onDeleteMedicalCenterAction(BaseEvent $event)
    {
        $object = $event->getData();
        $param = array('key' => array((int)$object->getId(), AlertClasses::INSTITUTION_MEDICAL_CENTER));

        $alerts = $this->alertService->getAlerts(AlertService::REFERENCE_ALERT_VIEW_URI, $param);

        if(count($alerts)) {
            for($i=0; $i<count($alerts); $i++) {
                $alerts[$i]['_deleted'] = true;
            }

            $this->alertService->multipleUpdate($alerts);
        }
    }
    

    /// Private Functions ///
    private function createApprovedListingAlert($object, $options)
    {
        $referenceData = array(
            'id' => (int)$object->getId(),
            'name' => $object->getMedicalCenter()->getName()
        );

        $pendingAlert = array(
            'recipient' => $options->get('institutionId'),
            'recipientType' => AlertRecipient::INSTITUTION,
            'referenceData' => $referenceData,
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::APPROVED_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => false
        );
    
        return $pendingAlert;
    }
    
    private function createExpiredListingAlerts($object, $options)
    {
        $referenceData = array(
            'id' => (int)$object->getId(),
            'name' => $object->getMedicalCenter()->getName(),
            'institutionId' => $options->get('institutionId')
        );

        // Set 30 Days to Expire Listing
        $dateAlert = strtotime('+30 day', strtotime(date(AlertService::DATE_FORMAT)));
        $dateAlert = date(AlertService::DATE_FORMAT, $dateAlert);

        $adminExpiredAlert = array(
            'recipient' => null,
            'recipientType' => AlertRecipient::ALL_ACTIVE_ADMIN,
            'referenceData' => $referenceData,
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::EXPIRED_LISTING,
            'dateAlert' => $dateAlert,
            'isDeletable' => true
        );

        $institutionExpiredAlert = $adminExpiredAlert;
        $institutionExpiredAlert['recipient'] = $options->get('institutionId');
        $institutionExpiredAlert['recipientType'] = AlertRecipient::INSTITUTION;

        return array($adminExpiredAlert, $institutionExpiredAlert);
    }

    private function createPendingListingAlert($object, $options)
    {        
        $referenceData = array(
            'id' => (int)$object->getId(),
            'name' => $object->getMedicalCenter()->getName(),
            'institutionId' => $options->get('institutionId')
        );

        $pendingAlert = array(
            'recipientType' => AlertRecipient::ALL_ACTIVE_ADMIN,
            'referenceData' => $referenceData,
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::PENDING_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => false
        );
        
        return $pendingAlert;
    }
    
    private function createDraftListingAlert($object, $options)
    {
        $draftAlert = array(
            'recipient' => $options->get('institutionId'),
            'recipientType' => AlertRecipient::INSTITUTION,
            'referenceData' => array('id' => $object->getId(), 'name' => $object->getMedicalCenter()->getName()),
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::DRAFT_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => false
        );
        
        return $draftAlert;
    }

    private function createRemoveDraftAlert($object)
    {
        $param = array('key' => array(AlertTypes::DRAFT_LISTING, (int)$object->getId(), AlertClasses::INSTITUTION_MEDICAL_CENTER));
        $result = $this->alertService->getAlerts(AlertService::TYPE_AND_REFERENCE_ALERT_VIEW_URI, $param);


        if(count($result)) {
            $draftAlert = $result[0];
            $draftAlert['_deleted'] = true;
        } else {
            $draftAlert = null;
        } 

        return $draftAlert;
    }

    private function createRemovePendingAlert($object, $options)
    {
        $param = array('key' => array(AlertTypes::PENDING_LISTING, (int)$object->getId(), AlertClasses::INSTITUTION_MEDICAL_CENTER));
        $result = $this->alertService->getAlerts(AlertService::TYPE_AND_REFERENCE_ALERT_VIEW_URI, $param);

        if(count($result)) {
            $pendingAlert = $result[0];
            $pendingAlert['_deleted'] = true;
        } else {
            $pendingAlert = null;
        }
    
        return $pendingAlert;
    }
}