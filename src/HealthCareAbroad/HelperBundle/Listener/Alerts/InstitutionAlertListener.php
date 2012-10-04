<?php
/**
 * Institution Alert Listener
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\HelperBundle\Listener\Alerts;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;
use HealthCareAbroad\HelperBundle\Services\AlertService;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InstitutionAlertListener extends BaseAlertListener
{    
    public function __construct(ContainerInterface $container)
    {        
        parent::__construct($container);
    }

    /**
     * 
     * @param BaseEvent $event
     */
    public function onAddMedicalCenterAction(BaseEvent $event)
    {
        $object = $event->getData();

        $draftAlert = array(
            'institutionId' => $event->getOption('institutionId'),
            'referenceData' => array('id' => $object->getId(), 'name' => $object->getMedicalCenter()->getName()),
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::DRAFT_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => false
        );

        $this->alertService->save($draftAlert);
    }

    
    public function onEditMedicalCenterAction(BaseEvent $event)
    {
        $object = $event->getData();

        if($event->getOption('previousStatus') == InstitutionMedicalCenterStatus::DRAFT) {
            $alertData = array();
            $object = $event->getData();            

            // REMOVE Draft Alert if exists!
            $alertId = $this->alertService->generateAlertId($object->getId(), AlertClasses::INSTITUTION_MEDICAL_CENTER, AlertTypes::DRAFT_LISTING);
            $draftAlert = $this->alertService->getAlert($alertId);
            
            if($draftAlert) {
                $draftAlert['_deleted'] = true;
                array_push($alertData, $draftAlert);
            }
            
            if($object->getStatus() == InstitutionMedicalCenterStatus::PENDING) {
                $pendingAlert = array(
                    'institutionId' => $event->getOption('institutionId'),
                    'referenceData' => array('id' => $object->getId(), 'name' => $object->getMedicalCenter()->getName()),
                    'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
                    'type' => AlertTypes::PENDING_LISTING,
                    'dateAlert' => date(AlertService::DATE_FORMAT),
                    'isDeletable' => false
                );

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

        $data = array(
            'institutionId' => $event->getOption('institutionId'),
            'referenceData' => array('id' => $object->getId(), 'name' => $object->getMedicalCenter()->getName()),
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::DRAFT_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT),
            'isDeletable' => true
        );

        switch($object->getStatus()) {
            case InstitutionMedicalCenterStatus::PENDING :
                $pendingAlert = $data;
                $pendingAlert['type'] = AlertTypes::PENDING_LISTING;
                $pendingAlert['recipient'] = AlertService::ADMIN_RECIPIENT;
                $pendingAlert['referenceData']['institutionId'] = $pendingAlert['institutionId'];

                unset($pendingAlert['institutionId']);
                array_push($alertData, $pendingAlert);

                // REMOVE Draft Alert if exists!
                $alertId = $this->alertService->generateAlertId($object->getId(), AlertClasses::INSTITUTION_MEDICAL_CENTER, AlertTypes::DRAFT_LISTING);
                $draftAlert = $this->alertService->getAlert($alertId);
                
                if($draftAlert) {
                    $draftAlert['_deleted'] = true;
                    array_push($alertData, $draftAlert);
                }
                break;

            case InstitutionMedicalCenterStatus::APPROVED :
                
                // Add Approved Alert
                $approvedAlert = $data;
                $approvedAlert['type'] = AlertTypes::APPROVED_LISTING;
                array_push($alertData, $approvedAlert);

                // ADD Expired Alert
                $expiredAlert = $data;
                $dateAlert = strtotime('+30 day', strtotime(date(AlertService::DATE_FORMAT)));
                $dateAlert = date(AlertService::DATE_FORMAT, $dateAlert);
                $expiredAlert['dateAlert'] = $dateAlert;
                $expiredAlert['type'] = AlertTypes::EXPIRED_LISTING;
                array_push($alertData, $expiredAlert);
                
                // REMOVE Pending Alert if exists!
                $alertId = $this->alertService->generateAlertId($object->getId(), AlertClasses::INSTITUTION_MEDICAL_CENTER, AlertTypes::PENDING_LISTING);
                $pendingAlert = $this->alertService->getAlert($alertId);
                
                if($pendingAlert) {
                    $pendingAlert['_deleted'] = true;
                    array_push($alertData, $pendingAlert);
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
        $alertId = $this->alertService->generateAlertId($object->getId(), AlertClasses::INSTITUTION_MEDICAL_CENTER, AlertTypes::DRAFT_LISTING);

        $alertData = $this->alertService->getAlert($alertId);

        if($alertData) {
            $this->alertService->delete($alertId, $alertData['_rev']);
        }
    }
    
    /**
     * 
     * @param BaseEvent $event
     */

}