<?php

namespace HealthCareAbroad\StatisticsBundle\Exception;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

class TrackerException extends \Exception
{
    static public function trackerClassDoesNotExist($className)
    {
        return new self("Tracker class {$className} does not exist.");
    }
    
    static public function invalidTrackerClass($trackerObj)
    {
        $classInstance = ($parentClass=\get_parent_class($trackerObj)) ? $parentClass : get_class($trackerObj);
        return new self("Expecting HealthCareAbroad\StatisticsBundle\Services\Trackers\Tracker object instance. {$classInstance} given.");
    }
    
    static public function unknownTrackerType($type)
    {
        return new self("Unknown tracker type {$className}. Possible types: ", \implode(', ', \array_keys(StatisticTypes::getTypes())));
    }
    
    static public function unknownAdvertisementDataType()
    {
        return new self("Unknown advertisement data type");
    }
}