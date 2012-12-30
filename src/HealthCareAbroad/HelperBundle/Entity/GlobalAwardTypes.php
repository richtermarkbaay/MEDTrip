<?php
namespace HealthCareAbroad\HelperBundle\Entity;

final class GlobalAwardTypes
{
    const AWARD = 1;
    
    const CERTIFICATE = 2;
    
    const AFFILIATION = 3;
    
    private static $types = array();
    
    private static $typeKeys = array();
    
    public static function getTypes()
    {
        return static::$types;
    }
    
    static public function getTypeKeys()
    {
        return static::$typeKeys;
    }
    
    static public function _initTypes()
    {
        static::$types = array(
            self::AWARD => 'Award',
            self::CERTIFICATE => 'Certificate',
            self::AFFILIATION => 'Affiliation'
        );
        
        static::$typeKeys = array(
            GlobalAwardTypes::AWARD => 'award',
            GlobalAwardTypes::CERTIFICATE => 'certificate',
            GlobalAwardTypes::AFFILIATION => 'affiliation',
        );
    }
}

GlobalAwardTypes::_initTypes();