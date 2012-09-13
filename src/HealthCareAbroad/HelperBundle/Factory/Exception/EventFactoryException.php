<?php
namespace HealthCareAbroad\HelperBundle\Factory\Exception;

class EventFactoryException extends \Exception 
{
    public static function eventClassParameterNotFound($event)
    {
        return new self("Event class parameter for {$event} event does not exist.");
    }
    
    public static function eventClassNotFound($className)
    {
        return new self("Event class {$className} not found.");
    }
    
    public static function invalidEventClass($class)
    {
        return new self("{$class} should be an instance of Symfony\Component\EventDispatcher\Event");
    }
}