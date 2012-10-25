<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;

final class InstitutionMedicalCenterStatus {

    const INACTIVE = 1;

    const APPROVED = 2;

    const DRAFT = 4;

    const PENDING = 8;

    const EXPIRED = 16;

    const ARCHIVED = 32;

    static public function getStatusList()
    {
        return array(
            self::INACTIVE => 'Inactive',
            self::APPROVED => 'Approved',
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::EXPIRED => 'Expired',
            self::ARCHIVED => 'Archived'
        );
    }

    static public function getStatusListForInstitutionContext()
    {
        return array(
            self::APPROVED => 'Active',
            self::DRAFT => 'Draft',
            self::PENDING => 'Pending',
            self::EXPIRED => 'Expired'
        );
    }

    static public function getUpdateStatusOptions()
    {
        return array(
            'Approve / Activate' => self::APPROVED,
            'Deny / Remove' => self::ARCHIVED
        );
    }

    static public function isValid($status)
    {
        return in_array($status, array_keys(self::getStatusList()));
    }

}