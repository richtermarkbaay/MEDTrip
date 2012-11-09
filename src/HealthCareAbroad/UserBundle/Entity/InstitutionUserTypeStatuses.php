<?php
namespace HealthCareAbroad\UserBundle\Entity;

final class InstitutionUserTypeStatuses
{
    /**
     * User types that are built-in to the system and therefore not editable
     */
    const BUILT_IN = 1;
    
    const ACTIVE = 2;
    
    const INACTIVE = 4;
    
    static public function getBitValueForBuiltInUserType()
    {
        return self::ACTIVE + self::BUILT_IN;
    }
}