<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;

final class InstitutionTypes
{
    const MEDICAL_GROUP_NETWORK_MEMBER = 1;

    const MEDICAL_TOURISM_FACILITATOR = 2;
    
    const INDEPENDENT_HOSPITAL = 3;
    
    static public function getList()
    {
        return array(
            self::MEDICAL_GROUP_NETWORK_MEMBER => 'Hospital or clinic belonging to a Group / Network',
            self::MEDICAL_TOURISM_FACILITATOR => 'Medical tourism facilitator / Agent',
            self::INDEPENDENT_HOSPITAL => 'Independent hospital or clinic'
        );
    }
    
    static public function getDiscriminatorMapping()
    {
        return array(
            self::MEDICAL_GROUP_NETWORK_MEMBER => 'HealthCareAbroad\InstitutionBundle\Entity\MedicalGroupNetworkMember',
            self::MEDICAL_TOURISM_FACILITATOR => 'HealthCareAbroad\InstitutionBundle\Entity\MedicalTourismFacilitator',
            self::INDEPENDENT_HOSPITAL => 'HealthCareAbroad\InstitutionBundle\Entity\IndependentHospital'
        );
    }
}