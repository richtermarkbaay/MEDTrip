<?php

namespace HealthCareAbroad\AdvertisementBundle\Exception;

class AdvertisementPropertyException extends \Exception
{
    static public function unavailablePropertyType($propertyTypeName)
    {
        return new self(sprintf("Property type %s does not exist or not available.", $propertyTypeName));
    }
    
    static public function emptyPropertyClass($propertyName)
    {
        return new self(sprintf("Invalid Class given empty string for property %s.", $propertyName));
    }
}