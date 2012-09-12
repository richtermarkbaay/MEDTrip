<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;

final class InstitutionStatus {
    
    const ACTIVE = 1;
    
    const INACTIVE = 2;
    
    const UNAPPROVED = 4;
    
    const APPROVED = 8;
    
    const SUSPENDED = 16;
    
    const USER_TYPE = "SUPER_ADMIN";
    
    public static function getStatusList()
    {
        return array(
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::APPROVED => 'Approved',
            self::UNAPPROVED => 'Unapproved',
            self::SUSPENDED => 'Suspended'
        );
    }

    public static function getUpdateStatusOptions()
    {
        return array(
            'Activate' => self::ACTIVE,
            'Approve' => self::APPROVED,
            'Suspend' => self::SUSPENDED
        );
    }
    
    public static function isValid($value) {
        return in_array($value, array_keys(self::getStatusList()));
    }
}