<?php 

namespace HealthCareAbroad\FrontendBundle\Services;

class FrontendMemcacheKeysHelper
{
    const INSTITUTION_PROFILE_KEY = 'frontend.controller.institution_profile.{ID}';

    const INSTITUTION_MEDICAL_CENTER_PROFILE_KEY = 'frontend.controller.institutionMedicalCenter:profile.{ID}';


    static function generateInsitutionProfileKey($instititionId = '')
    {
        return str_replace('{ID}', $instititionId, self::INSTITUTION_PROFILE_KEY);
    }

    static function generateInsitutionMedicalCenterProfileKey($centerId = '')
    {
        return str_replace('{ID}', $centerId, self::INSTITUTION_MEDICAL_CENTER_PROFILE_KEY);
    }
}