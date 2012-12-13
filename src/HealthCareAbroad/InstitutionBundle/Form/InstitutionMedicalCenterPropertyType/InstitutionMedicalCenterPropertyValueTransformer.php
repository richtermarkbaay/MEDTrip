<?php

namespace HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterPropertyType;

use Symfony\Component\Form\DataTransformerInterface;

class InstitutionMedicalCenterPropertyValueTransformer implements DataTransformerInterface
{
    
    public function transform($data)
    {
        
    }
    
    public function reverseTransform($value)
    {
        if (\is_object($value) ){
            if (!\method_exists($value, 'getId')) {
                throw new \Exception(__CLASS__.' cannot reverse transform '.\get_class($value).' without id accessor.');
            }
            
            return $value->getId();
        }
        
        return $value;
    }
    
}