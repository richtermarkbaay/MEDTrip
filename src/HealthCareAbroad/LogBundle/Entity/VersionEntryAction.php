<?php
namespace HealthCareAbroad\LogBundle\Entity;

use HealthCareAbroad\LogBundle\Entity\VersionEntry;

final class VersionEntryAction {
    
    const CREATE = 1;
    
    const UPDATE = 2;
    
    const REMOVE = 3;
    
    static function getActionOptionsList()
    {
        return array(
            'Create' => 'create',
            'Update' => 'update',
            'Remove' => 'remove'
        );
    }
}