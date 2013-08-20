<?php
namespace HealthCareAbroad\HelperBundle\Entity;

final class GlobalAwardTypes
{
    const AWARD = 1;
    
    const CERTIFICATE = 2;
    
    const AFFILIATION = 3;
    
    const ACCREDITATION = 4;
    
    private static $types = array();
    
    private static $typeKeys = array();
    
    public static function getTypes()
    {
        return static::$types;
    }
    
    /**
     * Get type value
     * @param int $type
     */
    public static function getTypeValue($type)
    {
        return \array_key_exists($type, static::$types) ? static::$types[$type] : null;
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
            self::AFFILIATION => 'Affiliation',
            self::ACCREDITATION => 'Accreditation'
        );
        
        static::$typeKeys = array(
            GlobalAwardTypes::AWARD => 'award',
            GlobalAwardTypes::CERTIFICATE => 'certificate',
            GlobalAwardTypes::AFFILIATION => 'affiliation',
            self::ACCREDITATION => 'accreditation'
        );
    }
}

GlobalAwardTypes::_initTypes();