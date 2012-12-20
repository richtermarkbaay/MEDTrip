<?php
namespace HealthCareAbroad\HelperBundle\Entity;

final class GlobalAwardTypes
{
    const AWARD = 1;
    
    const CERTIFICATE = 2;
    
    const AFFILIATION = 3;
    
    private static $types = array();
    
    public static function getTypes()
    {
        return static::$types;
    }
    
    static public function _initTypes()
    {
        static::$types = array(
            self::AWARD => 'Award',
            self::CERTIFICATE => 'Certificate',
            self::AFFILIATION => 'Affiliation'
        );
    }
}

GlobalAwardTypes::_initTypes();