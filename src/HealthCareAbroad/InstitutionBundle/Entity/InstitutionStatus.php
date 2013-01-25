<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

final class InstitutionStatus {

    const ACTIVE = 1;

    const INACTIVE = 2;

    const UNAPPROVED = 4;

    const APPROVED = 8;

    const SUSPENDED = 16;

    const BLOCKED = 32;

    const USER_TYPE = "SUPER_ADMIN";

    private static $bits;

    private static $bitValueLabels;

    public static function getStatusList()
    {
        return array(
            self::ACTIVE => 'Active',
            self::INACTIVE => 'New Account',
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

    /**
     * Get the bit equivalent value for APPROVED status
     */
    public static function getBitValueForApprovedStatus()
    {
        return static::getBitValue(static::APPROVED);
    }

    public static function getBitValueForSuspendedStatus()
    {
        return static::getBitValue(static::SUSPENDED);
    }

    public static function getBitValueForUnapprovedStatus()
    {
        return static::getBitValue(static::UNAPPROVED);
    }

    public static function getBitValueForActiveStatus()
    {
        return self::ACTIVE;
    }

    public static function getBitValueForInactiveStatus()
    {
        return self::INACTIVE;
    }

    public static function getBitValueForBlockedStatus()
    {
        return self::BLOCKED;
    }

    public static function getBitValueForActiveAndApprovedStatus()
    {
        return self::ACTIVE | self::APPROVED;
    }

    /**
     * Get bit value of status
     * @param int $status
     */
    public static function getBitValue($status)
    {
        return \array_key_exists($status, static::$bits) ? static::$bits[$status] : null;
    }

    public static function getBitValueLabels()
    {
        return static::$bitValueLabels;
    }

    public static function _setStaticValues()
    {
        static::$bits = array(
            self::ACTIVE => self::ACTIVE,
            self::INACTIVE => self::INACTIVE,
            self::APPROVED => self::ACTIVE + self::APPROVED,
            self::UNAPPROVED => self::ACTIVE + self::UNAPPROVED,
            self::SUSPENDED => self::ACTIVE + self::SUSPENDED
        );

        $list = static::getStatusList();
        static::$bitValueLabels = array();

        foreach (static::$bits as $key => $v) {
            if (\array_key_exists($key, $list)) {
                static::$bitValueLabels[$v] = $list[$key];
            }
        }
    }
}

InstitutionStatus::_setStaticValues();
