<?php
namespace HealthCareAbroad\LogBundle\Entity;

use HealthCareAbroad\LogBundle\Entity\VersionEntry;

final class VersionEntryActions {
    
    const CREATE = 1;
    
    const UPDATE = 2;
    
    const REMOVE = 3;
    
    static function getActionOptions()
    {
        return array(
            'Create' => self::CREATE,
            'Update' => self::UPDATE,
            'Remove' => self::REMOVE
        );
    }
    
    private static $actions = array();
    
    private static $actionKeys = array();
    
    public static function getActions()
    {
        return static::$actions;
    }
    
    static public function getActionKeys()
    {
        return static::$actionKeys;
    }
    
    static public function _initActions()
    {
        static::$actions = array(
                        self::CREATE => 'Create',
                        self::UPDATE => 'Update',
                        self::REMOVE => 'Remove',
        );
    
        static::$actionKeys = array(
                        VersionEntryActions::CREATE => 'Create',
                        VersionEntryActions::UPDATE => 'Update',
                        VersionEntryActions::REMOVE => 'Remove',
        );
    }
    
    static public function getActionLabel($action)
    {
        return isset(self::$actionKeys[$action]) ? self::$actionKeys[$action] : '';
    }
    
}
VersionEntryActions::_initActions();