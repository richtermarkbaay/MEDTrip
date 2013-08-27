<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;

final class InstitutionTypes
{
    const MULTIPLE_CENTER = 1;

    //const MEDICAL_TOURISM_FACILITATOR = 2;
    
    const SINGLE_CENTER = 3;
    
    public static function getLabelList()
    {
        return array(
            self::MULTIPLE_CENTER => 'A hospital with many clinics, centers or units',
            self::SINGLE_CENTER => 'A single clinic or an independent healthcare provider',
            //self::MEDICAL_TOURISM_FACILITATOR => 'A Medical Tourism Facilitator / Agent',
        );
    }
    
    static public function getFormChoices()
    {
        return array(
            self::MULTIPLE_CENTER => 'A hospital with many clinics, centers or units',
            self::SINGLE_CENTER => 'A single clinic or an independent healthcare provider',
            //self::MEDICAL_TOURISM_FACILITATOR => 'I\'m a Medical Tourism Facilitator / Agent',
        );
    }
    
//     static public function getDiscriminatorMapping()
//     {
//         return array(
//             self::MULTIPLE_CENTER => 'HealthCareAbroad\InstitutionBundle\Entity\MedicalGroupNetworkMember',
//             self::MEDICAL_TOURISM_FACILITATOR => 'HealthCareAbroad\InstitutionBundle\Entity\MedicalTourismFacilitator',
//             self::SINGLE_CENTER => 'HealthCareAbroad\InstitutionBundle\Entity\IndependentHospital'
//         );
//     }
}