<?php
/**
 * 
 * @author Adelbert Silla
 *
 */
namespace HealthCareAbroad\HelperBundle\Listener\Alerts;

final class AlertClasses 
{
    const INSTITUTION = 1;
    const INSTITUTION_MEDICAL_CENTER = 2;
    const INSTITUTION_MEDICAL_PROCEDURE_TYPE = 3;
    const INSTITUTION_MEDICAL_PROCEDURE = 4;

    const INSTITUTION_BUNDLE_NS = "HealthCareAbroad\\InstitutionBundle\\Entity\\";

    /**
     * 
     * @param int $id
     * @return Ambigous <string>
     */
    static function getClassName($id)
    {
        $classes = self::getClasses();
        if(!isset($classes[$id])) {
            // Show Error
        }

        return $classes[$id];
    }

    /**
     * 
     * @return multitype:array
     */
    static function getClasses()
    {
        return array(
            self::INSTITUTION =>  self::INSTITUTION_BUNDLE_NS . 'Institution',
            self::INSTITUTION_MEDICAL_CENTER => self::INSTITUTION_BUNDLE_NS . 'InstitutionMedicalCenter',
            self::INSTITUTION_MEDICAL_PROCEDURE_TYPE => self::INSTITUTION_BUNDLE_NS . 'InstitutionMedicalProcedureType',
            self::INSTITUTION_MEDICAL_PROCEDURE => self::INSTITUTION_BUNDLE_NS . 'InstitutionMedicalProcedure',
        );
    }
    
    
    /**
     * 
     * @param int $id
     * @return boolean
     */
    static function isValidClass($id)
    {
        return in_array($id, array_keys(self::getClasses()));
    }
}