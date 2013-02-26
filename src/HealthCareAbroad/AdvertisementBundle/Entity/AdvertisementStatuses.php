<?php
namespace HealthCareAbroad\AdvertisementBundle\Entity;

final class AdvertisementStatuses
{
    const INACTIVE = 1;
    
    const ACTIVE = 2;
    
    const EXPIRED = 4;
    
    const ARCHIVED = 8;
    
    
    static function getList()
    {
        return array(
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::EXPIRED => 'Expired',
            self::ARCHIVED => 'Archived' 
        );
    }
}