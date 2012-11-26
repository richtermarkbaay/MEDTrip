<?php
namespace HealthCareAbroad\HelperBundle\Exception;

class ListFilterException extends \Exception
{
    static public function unregisteredServiceDependency($serviceId)
    {
        return new self("Trying to call unregisted service dependency {$serviceId}.");
    }
}