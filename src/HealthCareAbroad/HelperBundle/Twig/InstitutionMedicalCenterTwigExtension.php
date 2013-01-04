<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

class InstitutionMedicalCenterTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'get_medical_center_status_label' => new \Twig_Function_Method($this, 'getStatusLabel'),
        );
    }
    
    public function getStatusLabel(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $statuses = InstitutionMedicalCenterStatus::getStatusList();
        
        return $statuses[$institutionMedicalCenter->getStatus()];
    }
    
    public function getName()
    {
        return 'institutionMedicalCenterExtension';
    }
}