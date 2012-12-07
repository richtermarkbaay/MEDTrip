<?php

namespace HealthCareAbroad\InstitutionBundle\Form\InstitutionPropertyType;

use Symfony\Component\Form\DataTransformerInterface;

class InstitutionPropertyValueTransformer implements DataTransformerInterface
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