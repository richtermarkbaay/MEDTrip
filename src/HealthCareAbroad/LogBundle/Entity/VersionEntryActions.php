<?php
namespace HealthCareAbroad\LogBundle\Entity;

use HealthCareAbroad\LogBundle\Entity\VersionEntry;

final class VersionEntryActions {
    
    const CREATE = 'Create';
    
    const UPDATE = 'Update';
    
    const REMOVE = 'Remove';
    
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
            VersionEntryActions::CREATE => 'create',
            VersionEntryActions::UPDATE => 'update',
            VersionEntryActions::REMOVE => 'remove',
        );
    }
    
    static public function getActionLabel($action)
    {
        return isset(self::$actions[$action]) ? self::$actions[$action] : '';
    }
    
}
VersionEntryActions::_initActions();