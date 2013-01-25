<?php
namespace HealthCareAbroad\AdvertisementBundle\Entity;
 
use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

final class AdvertisementHighlightType
{
    const AWARD = 1;
    const SERVICE = 2;
    const DOCTOR = 3;
    const TREATMENT = 4;
    const SPECIALIZATION = 5;
    const CLINIC = 6;
    
    public static function getList()
    {
        return array(
            self::AWARD => 'Award',
            self::SERVICE => 'Service',
            self::DOCTOR => 'Doctor',
            self::TREATMENT => 'Treatment',
            self::SPECIALIZATION => 'Specialization',
            self::CLINIC => 'Clinic'
        );
    } 
}