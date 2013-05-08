<?php

namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\InstitutionBundle\Entity\BusinessHour;

use Symfony\Component\Form\DataTransformerInterface;

class BusinessHourEntityDataTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if ($value instanceof BusinessHour) {
            $value = \json_encode(array(
                'weekdayBitValue' => $value->getWeekdayBitValue(),
                'opening' => $value->getOpening(),
                'closing' => $value->getClosing(),
                'notes' => $value->getNotes()
            ));
        }
        
        return $value;
    }
    
    public function reverseTransform($value)
    {
        if ($value instanceof BusinessHour) {
            return $value;
        }
        
        $value = \stripslashes($value);
        $decodedValue = \json_decode($value, true);
        $obj = null;
        if ($decodedValue) {
            $obj = new BusinessHour();
            $obj->setWeekdayBitValue($decodedValue['weekdayBitValue']);
            $obj->setOpening(new \DateTime($decodedValue['opening']));
            $obj->setClosing(new \DateTime($decodedValue['closing']));
            $obj->setNotes($decodedValue['notes']);
        }
        
        return $obj;
    }
}