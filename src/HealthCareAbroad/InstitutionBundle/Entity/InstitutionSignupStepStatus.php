<?php
/**
 *  
 * @author Adelbert Silla
 * @deprecated Now catered in SignUpService and SignUpTwigExtension 
 */
namespace HealthCareAbroad\InstitutionBundle\Entity;

// final class InstitutionSignupStepStatus {

//     const FINISH = 0;
//     const STEP1 = 1;
//     const STEP2 = 2;
//     const STEP3 = 3;
//     const STEP4 = 4;
//     const STEP5 = 5; 

//     protected static $routeNames = array(
//         self::FINISH => 'institution_homepage',
//         self::STEP1 => 'institution_signup_setup_profile',
//         self::STEP2 => 'institution_medicalCenter_addSpecializations', // requires imcId params
//         self::STEP3 => 'institution_medicalCenter_addAncilliaryServices', // requires imcId params
//         self::STEP4 => 'institution_medicalCenter_addGlobalAwards', // requires imcId params
//         self::STEP5 => 'institution_add_medical_specialist' // requires imcId params
//     );

//     public static function getRouteNames()
//     {
//         return self::$routeNames;
//     }

//     public static function getRouteNameByStatus($signupStepStatus)
//     { 
//         return self::$routeNames[$signupStepStatus];
//     }

//     public static function isValidStatus($signupStepStatus)
//     {
//         return isset(self::$routeNames[$signupStepStatus]);
//     }
    
//     public static function hasCompletedSteps($signupStepStatus)
//     {
//         return $signupStepStatus === self::FINISH;
//     }
    
//     public static function isStep1($signupStepStatus)
//     {
//         return (int)$signupStepStatus === self::STEP1;
//     }
    
//     public static function getMultipleCenterRouteNameByStatus($signupStepStatus)
//     {
//         $status = self::getMultipleCenterRouteNames();
//         return $status[$signupStepStatus];
//     }
    
//     public static function getMultipleCenterRouteNames()
//     {
//         return array(
//             self::FINISH => 'institution_medicalCenter_edit', // requires imcId params
//             self::STEP1 => 'institution_signup_setup_profile'
//         );
//     }
// }