<?php
/**
 * Institution Alert Listener
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\HelperBundle\Listener\Alerts;

use HealthCareAbroad\HelperBundle\Services\AlertService;

use HealthCareAbroad\HelperBundle\Entity\Alert;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Bundle\DoctrineBundle\Registry;

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
    public function onAddMedicalCenterAlertAction(BaseEvent $event)
    {
        $this->updateAlertData($event);
    }

    /**
     *
     * @param BaseEvent $event
     */
    public function onEditMedicalCenterAlertAction(BaseEvent $event)
    {
        $this->updateAlertData($event);
    }
    
    /**
     *
     * @param BaseEvent $event
     */
    public function onDeleteMedicalCenterAlertAction(BaseEvent $event)
    {
        $object = $event->getData();

        $alertData = array(
            'referenceData' => array('id' => $object->getId()),
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::DRAFT_LISTING
        );

        $alertId = $this->alertService->generateAlertId($alertData['referenceData']['id'], $alertData['class'], $alertData['type']);

        $alertData = $this->alertService->getAlert($alertId);

        if($alertData) {
            $this->alertService->delete($alertId, $alertData['_rev']);
        }
    }
    
    /**
     * 
     * @param BaseEvent $event
     */
    private function updateAlertData(BaseEvent $event)
    {
        $alertData = array();
        $object = $event->getData();

        $data = array(
            'institutionId' => $event->getOption('institutionId'),
            'referenceData' => array('id' => $object->getId(), 'name' => $object->getMedicalCenter()->getName()),
            'class' => AlertClasses::INSTITUTION_MEDICAL_CENTER,
            'type' => AlertTypes::DRAFT_LISTING,
            'dateAlert' => date(AlertService::DATE_FORMAT)
        );
        
        switch($object->getStatus()) {

            case InstitutionMedicalCenterStatus::DRAFT :
                array_push($alertData, $data);
                break;

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

        $this->alertService->save($alertData);
        
        //exit;
    }

}