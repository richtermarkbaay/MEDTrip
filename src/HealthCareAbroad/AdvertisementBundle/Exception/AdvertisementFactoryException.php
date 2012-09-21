<?php
namespace HealthCareAbroad\AdvertisementBundle\Exception;

class AdvertisementFactoryException extends \Exception
{
    static public function unknownDiscriminatorType($type)
    {
        return new self("Unknown discriminator type {$type}.");
    }
}