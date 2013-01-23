<?php
/**
 *  
 * @author Adelbert Silla
 * 
 */
namespace HealthCareAbroad\InstitutionBundle\Entity;

final class InstitutionSignupStepStatus {

    const FINISH = 0;
    const STEP1 = 1;
    const STEP2 = 2;
    const STEP3 = 3;
    const STEP4 = 4;
    const STEP5 = 5; 

    protected static $routeNames = array(
        self::FINISH => 'institution_homepage',
        self::STEP1 => 'institution_signup_complete_profile',
        self::STEP2 => 'institution_medicalCenter_addSpecializations',
        self::STEP3 => 'institution_medicalCenter_addAncilliaryServices',
        self::STEP4 => 'institution_medicalCenter_addGlobalAwards',
        self::STEP5 => 'institution_add_medical_specialist'
    );

    public static function getRouteNames()
    {
        return self::$routeNames;
    }

    public static function getRouteNameByStatus($signupStepStatus)
    { 
        return self::$routeNames[$signupStepStatus];
    }

    public static function isValidStatus($signupStepStatus)
    {
        return isset(self::$routeNames[$signupStepStatus]);
    }
    
    public static function hasCompletedSteps($signupStepStatus)
    {
        return $signupStepStatus === self::FINISH;
    }
    
    public static function isStep1($signupStepStatus)
    {
        return (int)$signupStepStatus === self::STEP1;
    }
    
    public static function getMultipleCenterRouteNameByStatus($signupStepStatus)
    {
        $status = self::getMultipleCenterRouteNames();
        return $status[$signupStepStatus];
    }
    
    public static function getMultipleCenterRouteNames()
    {
        return array(
            self::FINISH => 'institution_medicalCenter_index',
            self::STEP1 => 'institution_signup_complete_profile'
        );
    }
}