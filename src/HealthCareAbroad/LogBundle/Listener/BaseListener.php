<?php
/**
 * Base class for common log listeners
 * 
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\LogBundle\Listener;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

use Doctrine\Bundle\DoctrineBundle\Registry;

abstract class BaseListener
{
    /**
     * @var Registry
     */
    protected $doctrine;
    
    protected $logRepository;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    abstract public function onAdd(BaseEvent $event);
    
    abstract public function onEdit(BaseEvent $event);
    
    abstract public function onDelete(BaseEvent $event);
    
    protected function saveLog()
    {
        //TODO: implement this
    }
} 