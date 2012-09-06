<?php
namespace HealthCareAbroad\InstitutionBundle\Entity;
final class InstitutionMedicalCenterStatus {
    
    const INACTIVE = 0;
    
    const APPROVED = 1;
    
    const DRAFT = 2;
    
    const PENDING = 3;
    
    const EXPIRED = 4;
    
    const ARCHIVED = 5;
    
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