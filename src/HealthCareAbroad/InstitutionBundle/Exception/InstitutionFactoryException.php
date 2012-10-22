<?php
namespace HealthCareAbroad\InstitutionBundle\Exception;

class InstitutionFactoryException extends \Exception
{
    static public function invalidDiscriminator($type)
    {
        return new self('Unknown institution discriminator '.$type);
    }    
}