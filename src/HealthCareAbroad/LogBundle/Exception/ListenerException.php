<?php

namespace HealthCareAbroad\LogBundle\Exception;

class ListenerException extends \Exception
{
    static public function logClassDoesNotExist($className)
    {
        return new self("Log class {$className} not found.");
    }
}