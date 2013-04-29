<?php
namespace HealthCareAbroad\HelperBundle\Entity;

final class ContactDetailTypes
{
    const PHONE = 1;
    
    const MOBILE = 2;
    
    const FAX = 3;
    
    
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
            self::PHONE => 'Phone',
            self::MOBILE => 'Mobile',
            self::FAX => 'Fax',
        );
        
        static::$typeKeys = array(
            ContactDetailTypes::PHONE => 'phone',
            ContactDetailTypes::MOBILE => 'mobile',
            ContactDetailTypes::FAX => 'fax',
        );
    }
}

ContactDetailTypes::_initTypes();