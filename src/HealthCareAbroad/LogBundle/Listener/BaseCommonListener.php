<?php
/**
 * This is the base class for log common listener
 *
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\LogBundle\Listener;

use HealthCareAbroad\LogBundle\Entity\Log;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

use HealthCareAbroad\LogBundle\Services\LogService;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Bundle\DoctrineBundle\Registry;
use HealthCareAbroad\LogBundle\Entity\LogEventData;

abstract class BaseCommonListener
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var integer
     */
    protected $loggedAccountId;

    /**
     * @var LogService
     */
    protected $logService;

    protected $applicationContext;

    /**
     * @var array
     */
    private static $logClassMap;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->doctrine = $this->container->get('doctrine');

        $this->logService = $this->container->get('services.log');

        // set logged account id
        $securityContext = $this->container->get('security.context', ContainerInterface::NULL_ON_INVALID_REFERENCE);
        if (null != $securityContext && null != $securityContext->getToken()) {
            $this->loggedAccountId = $this->container->get('session')->get('accountId', null);
        }
        else {
            $this->loggedAccountId = 0;
        }
    }

    /**
     * Common BaseEvent listener. This will create a new log of the event and persist it to the database.
     *
     * @param BaseEvent $event
     */
    public function onCommonLogAction(BaseEvent $event)
    {
        // event data is expected to be an instance of LogEventData
        $eventData = $event->getData();
        $logAction = $this->getLogActionOfEventName($event->getName());
        $log = new Log();
        $log->setAccountId($this->loggedAccountId);
        $log->setAction($logAction);
        $log->setApplicationContext($this->applicationContext);
        
        if ($eventData instanceof LogEventData) {
        	$log->setMessage($eventData->getMessage());
        	
        	$log->setData(\json_encode($eventData->getData()));
        	
        	// quick fix for login since no logged accountId is set yet
        	$dataArr = $eventData->getData();
        	if (isset($dataArr['accountId']) && $dataArr['accountId']) {
        		$log->setAccountId($dataArr['accountId']);
        	}
        	
        	
        	// we only cater those with LogEventData
        	$this->logService->save($log);
        }
        else {
            // for BC
        }        
    }

    /**
     * Convenience function to get the log action type based on the event name
     *
     * @param string $eventName
     */
    protected function getLogActionOfEventName($eventName)
    {
        // by convention event name should be informat event.entity.action
        $parts = preg_split('/\./', $eventName, -1, \PREG_SPLIT_NO_EMPTY);

        return $parts[\count($parts)-1];
    }
}