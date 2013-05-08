<?php

namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\InstitutionBundle\Entity\BusinessHour;

use Symfony\Component\Form\DataTransformerInterface;

class BusinessHourEntityViewTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if ($value instanceof BusinessHour) {
            return $value;
        }
        
        $decodedValue = \json_decode(\stripslashes($value), true);
        if ($decodedValue ) {
            $obj = new BusinessHour();
            $obj->setWeekdayBitValue($decodedValue['weekdayBitValue']);
            $obj->setOpening(new \DateTime($decodedValue['opening']['date']));
            $obj->setClosing(new \DateTime($decodedValue['closing']['date']));
            $obj->setNotes($decodedValue['notes']);
            return $obj;
        }
    }
    
    public function reverseTransform($value)
    {
        $decodedValue = \json_decode(\stripslashes($value), true);
        if ($decodedValue ) {
            $obj = new BusinessHour();
            $obj->setWeekdayBitValue($decodedValue['weekdayBitValue']);
            $obj->setOpening(new \DateTime($decodedValue['opening']));
            $obj->setClosing(new \DateTime($decodedValue['closing']));
            $obj->setNotes($decodedValue['notes']);
            return $obj;
        }
    }
}