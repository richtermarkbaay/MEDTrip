<?php
namespace HealthCareAbroad\LogBundle\Entity;

use HealthCareAbroad\LogBundle\Entity\VersionEntry;

final class VersionEntryActions {
    
    const CREATE = 'create';
    
    const UPDATE = 'update';
    
    const REMOVE = 'remove';
    
    private static $actions = array();
    
    static function getActionOptions()
    {
        return self::$actions;
    }
    
    public static function getActions()
    {
        return static::$actions;
    }
    
    static public function _initActions()
    {
    
        static::$actions = array(
            VersionEntryActions::CREATE => 'Create',
            VersionEntryActions::UPDATE => 'Update',
            VersionEntryActions::REMOVE => 'Remove',
        );
    }
    
    static public function getActionLabel($action)
    {
        return isset(self::$actions[$action]) ? self::$actions[$action] : '';
    }
    
}
VersionEntryActions::_initActions();