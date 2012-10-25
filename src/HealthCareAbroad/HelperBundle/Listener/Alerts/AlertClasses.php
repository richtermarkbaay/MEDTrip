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
    const INSTITUTION_TREATMENT = 2;
    const INSTITUTION_SUB_SPECIALIZATION = 3;
    const INSTITUTION_SPECIALIZATION = 4;
    const INSTITUTION_MEDICAL_CENTER = 5;


    const INSTITUTION_BUNDLE_NS = "HealthCareAbroad\\InstitutionBundle\\Entity\\";

    /**
     * 
     * @param unknown_type $id
     * @return Ambigous <NULL, string>
     */
    static function getClassName($id)
    {
        $classes = self::getClasses();

        return isset($classes[$id]) ? $classes[$id] : null;
    }

    /**
     * 
     * @return multitype:array
     */
    static function getClasses()
    {
        return array(
            self::INSTITUTION =>  self::INSTITUTION_BUNDLE_NS . 'Institution',
            self::INSTITUTION_TREATMENT => self::INSTITUTION_BUNDLE_NS . 'InstitutionTreatment',
            self::INSTITUTION_SUB_SPECIALIZATION => self::INSTITUTION_BUNDLE_NS . 'InstitutionSubSpecialization',
            self::INSTITUTION_SPECIALIZATION => self::INSTITUTION_BUNDLE_NS . 'InstitutionSpecialization',
            self::INSTITUTION_MEDICAL_CENTER => self::INSTITUTION_BUNDLE_NS . 'InstitutionMedicalCenter'
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