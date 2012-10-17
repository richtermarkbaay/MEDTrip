<?php
/**
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\HelperBundle\Listener\Alerts;

final class AlertTypes 
{
    const DEFAULT_TYPE = 1;
    const NEW_INSTITUTION = 2;
    
    const DRAFT_LISTING = 3;
    const EXPIRED_LISTING = 4;
    const APPROVED_LISTING = 5;
    const DENIED_LISTING = 6;
    const PENDING_LISTING = 7;

    const DAY_BEFORE_EXPIRE_LISTING = 9;
    const WEEK_BEFORE_EXPIRE_LISTING = 10;
    const MONTH_BEFORE_EXPIRE_LISTING = 11;


    static function isValid($type)
    {
        return in_array($type, self::getAll());
    }

    static function getAll()
    {
        $object = new \ReflectionClass('HealthCareAbroad\HelperBundle\Listener\Alerts\AlertTypes');

        return $object->getConstants();
    }
}