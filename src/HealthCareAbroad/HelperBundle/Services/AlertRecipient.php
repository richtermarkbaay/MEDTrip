<?php
/**
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\HelperBundle\Services;

final class AlertRecipient
{
    const ADMIN = 'adminUser';
    const INSTITUTION = 'institution';

    const ALL_ADMIN = 'allAdminUsers';
    const ALL_INSTITUTION = 'allInstitutions';

    const ALL_ACTIVE_ADMIN = 'allActiveAdminUsers';
    const ALL_ACTIVE_INSTITUTION = 'allActiveInstitutions';

    static function isValid($type)
    {
        return in_array($type, self::getAll());
    }

    static function getAll()
    {
        $object = new \ReflectionClass('HealthCareAbroad\HelperBundle\Services\AlertRecipient');

        return $object->getConstants();
    }
}