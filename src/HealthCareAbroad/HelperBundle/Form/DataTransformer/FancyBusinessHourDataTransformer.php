<?php

namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\InstitutionBundle\Entity\BusinessHour;

use Symfony\Component\Form\DataTransformerInterface;

class FancyBusinessHourDataTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!$value instanceof BusinessHour) {
            return new BusinessHour();
        }
    }
    
    public function reverseTransform($value)
    {
        
    }
}