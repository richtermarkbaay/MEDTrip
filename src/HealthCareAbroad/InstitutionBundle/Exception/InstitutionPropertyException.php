<?php

namespace HealthCareAbroad\InstitutionBundle\Exception;

class InstitutionPropertyException extends \Exception
{
    static public function unavailablePropertyType($propertyTypeName)
    {
        return new self(sprintf("Property type %s does not exist or not available.", $propertyTypeName));
    }
}