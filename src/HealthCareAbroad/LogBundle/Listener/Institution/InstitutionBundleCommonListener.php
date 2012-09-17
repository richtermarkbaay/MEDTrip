<?php

namespace HealthCareAbroad\LogBundle\Listener\Institution;

use HealthCareAbroad\HelperBundle\Classes\ApplicationContexts;

use HealthCareAbroad\LogBundle\Entity\Log;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

use HealthCareAbroad\LogBundle\Listener\BaseCommonListener;

class InstitutionBundleCommonListener extends BaseCommonListener
{
    public function onCommonLogAction(BaseEvent $event)
    {
        $eventObject = $event->getData();
        $logAction = $this->getLogActionOfEventName($event->getName());
        $logClass = $this->logService->getLogClassByName(\get_class($event->getData()));
        
        $log = new Log();
        $log->setAccountId($this->loggedAccountId);
        $log->setAction($logAction);
        $log->setApplicationContext(ApplicationContexts::INSTITUTION_ADMIN);
        $log->setObjectId($eventObject->getId());
        $log->setLogClass($logClass);
        $this->logService->save($log);
    }
}