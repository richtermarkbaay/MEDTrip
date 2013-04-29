<?php

namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\InstitutionBundle\Entity\BusinessHour;

use Symfony\Component\Form\DataTransformerInterface;

class BusinessHourEntityViewTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        $decodedValue = \json_decode(\stripslashes($value), true);
        if ($decodedValue ) {
            $obj = new BusinessHour();
            $obj->setWeekdayBitValue($decodedValue['weekdayBitValue']);
            $obj->setOpening(new \DateTime($decodedValue['opening']['date']));
            $obj->setClosing(new \DateTime($decodedValue['closing']['date']));
            
            return $obj;
        }
    }
    
    public function reverseTransform($value)
    {
        
    }
}