<?php
/**
 * @author Adelbert D. Silla
 * @desc Factory Class for ListFilter Classes
 */

namespace HealthCareAbroad\HelperBundle\Services\Filters;

use \Exception;

final class ListFilterFactory
{

    /**
     *
     * @param string $routeName
     * @param Doctrine $doctrine
     * @throws Exception
     * @return Object ListFilter type
     */
    public static function create($routeName = '', $doctrine)
    {
        if(!$routeName)
            throw new Exception('routeName is required for ListFilterFactory!');


        // Internal routes name start's with underscore '_'
        $isInternalRoute = substr($routeName,0 ,1) == '_';
        if(!self::isValidRouteName($routeName) && !$isInternalRoute)
            throw new Exception('Route name '.$routeName.' is not yet valid for ListFilter Object.');


        if(self::isValidRouteName($routeName)) {
            $className ="\\HealthCareAbroad\\HelperBundle\\Services\\Filters\\".self::getClassNameByRouteName($routeName);

            return new $className($doctrine);
        }
    }

    /**
     * @desc This is the list of valid ListFilter Classes.
     *          New ListFilter class should be added here first with the corresponding routeName as its key or index.
     *
     * @return multitype:string
     */
    static function getValidClasses()
    {
        return array(
            'admin_news_index' => 'NewsListFilter',
            'admin_city_index' => 'CityListFilter',
            'admin_country_index' => 'CountryListFilter',
            'admin_institution_index' => 'InstitutionListFilter',
            'admin_specialization_index' => 'MedicalCenterListFilter',
            'admin_procedureType_index' => 'MedicalProcedureTypeListFilter',
            'admin_treatmentProcedure_index' => 'MedicalProcedureListFilter',
            'admin_institution_manageCenterGroups' => 'InstitutionMedicalCenterGroupListFilter',
            'admin_institution_manageCenters' => 'InstitutionMedicalCenterListFilter',
//            'admin_institution_manageProcedureTypes' => 'InstitutionTreatmentProcedureTypeListFilter',
            'institution_medicalCenter_index' => 'InstitutionMedicalCenterListFilter',
            'institution_medicalCenterGroup_index' => 'InstitutionMedicalCenterGroupListFilter',
        	'admin_advertisement_index' => 'AdvertisementListFilter'
        );
    }

    /**
     * @param string $routeName
     * @return <string> Thel class name assigned for the routeName
     */
    static function getClassNameByRouteName($routeName)
    {
        $classes = self::getValidClasses();
        return $classes[$routeName];
    }

    /**
     *
     * @param string $routeName
     * @return boolean
     */
    static function isValidRouteName($routeName)
    {
        return in_array($routeName, array_keys(self::getValidClasses()));
    }
}