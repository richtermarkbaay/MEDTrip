<?php
/**
 * Factory class for creating event classes
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\HelperBundle\Factory;

use Symfony\Component\EventDispatcher\GenericEvent;

use HealthCareAbroad\HelperBundle\Event\BaseEvent;

use Symfony\Component\EventDispatcher\Event;

use HealthCareAbroad\HelperBundle\Factory\Exception\EventFactoryException;

use Symfony\Component\DependencyInjection\ContainerInterface;

class EventFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }   
    
    /**
     * Create an event class by event name. Event class should be declared as container parameter with $eventName as its key
     * 
     * @param string $eventName
     * @param object $data subject Entity of this event 
     * @param array $options Key-value pair array of options
     * @return Symfony\Component\EventDispatcher\Event
     */
    public function create($eventName, $data=null, $options=array())
    {
        try {
            $eventClass = $this->container->getParameter($eventName);
        }
        catch (\Exception $e) {
            throw EventFactoryException::eventClassParameterNotFound($eventName);
        }
        
        if (!\class_exists($eventClass)) {
            throw EventFactoryException::eventClassNotFound($eventClass);
        }
        
        $event = new $eventClass;
        if (!$event instanceof Event) {
            throw EventFactoryException::invalidEventClass(\get_class($class));
        }
        
        $event->setName($eventName);
        
        if ($event instanceof BaseEvent) {
            $event->setData($data);
            
            foreach ($options as $key => $value) {
                $event->addOption($key, $value);
            }
        }
        
        return $event;
    }
    
    
}