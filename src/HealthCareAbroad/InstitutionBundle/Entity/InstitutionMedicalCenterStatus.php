<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;
final class InstitutionMedicalCenterStatus {
    
    const INACTIVE = 1;
    
    const APPROVED = 2;
    
    const DRAFT = 4;
    
    const PENDING = 8;
    
    const EXPIRED = 16;
    
    const ARCHIVED = 32;
    
    static public function getStatusList()
    {
        return array(
            self::DRAFT => 'Draft',
            self::APPROVED => 'Active',
            self::PENDING => 'Pending',
            self::EXPIRED => 'Expired',
            self::ARCHIVED => 'Archived'                
        );
    }
    
}