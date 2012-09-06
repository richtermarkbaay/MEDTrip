<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;
final class InstitutionMedicalCenterStatus {
    
    const DRAFT = 0;
    
    const ACTIVE = 1;
    
    const PENDING = 2;
    
    const EXPIRED = 3;
    
    const ARCHIVED = 4;
    
    static public function getStatusList()
    {
        return array(
            self::DRAFT => 'Draft',
            self::ACTIVE => 'Active',
            self::PENDING => 'Pending',
            self::EXPIRED => 'Expired',
            self::ARCHIVED => 'Archived'                
        );
    }
    
}