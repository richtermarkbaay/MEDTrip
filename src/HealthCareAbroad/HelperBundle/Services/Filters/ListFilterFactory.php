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
     * @return ListFilter
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
            'admin_city_index' => 'GlobalCityListFilter',
            'admin_state_index' => 'GlobalStateListFilter',
            'admin_doctor_index' => 'DoctorListFilter',
            'admin_country_index' => 'GlobalCountryListFilter',
            'admin_institution_index' => 'InstitutionListFilter',
            'admin_specialization_index' => 'SpecializationListFilter',
            'admin_subSpecialization_index' => 'SubSpecializationListFilter',
            'admin_treatment_index' => 'TreatmentListFilter',
            'admin_institution_medicalCenter_index' => 'InstitutionMedicalCenterListFilter',
            'admin_institution_manageSpecializations' => 'InstitutionSpecializationListFilter',
//            'admin_institution_manageProcedureTypes' => 'InstitutionTreatmentProcedureTypeListFilter',
            'institution_specialization_index' => 'InstitutionSpecializationListFilter',
            'admin_advertisement_index' => 'AdvertisementListFilter',
            'admin_advertisementType_index' => 'AdvertisementTypeListFilter',
            'admin_user_index' => 'AdminUserListFilter',
            'admin_userType_index' => 'AdminUserTypeListFilter',
            'admin_userRole_index' => 'AdminUserRoleListFilter',
        	'admin_awardingBody_index' => 'AwardingBodyListFilter',
        	'admin_global_award_index' => 'GlobalAwardListFilter',
            'admin_inquire' => 'InquiryListFilter',
        	'admin_inquiry_institutionInquiries' => 'InstitutionInquiryListFilter',
            'admin_institution_contactInfoList' => 'InstitutionContactDetailListFilter',
            'admin_error_reports' => 'ErrorReportListFilter',
            'admin_feedback' => 'FeedbackListFilter',
            'admin_institution_ranking_index' => 'InstitutionRankingListFilter',
            'admin_center_ranking_index' => 'InstitutionMedicalCenterRankingListFilter',
            'admin_institution_medicalCenters' => 'InstitutionMedicalCentersViewListFilter',
            'admin_statistics_institution' => 'InstitutionStatisticsListFilter',
            'admin_statistics_institutionMedicalCenter' => 'InstitutionMedicalCenterStatisticsListFilter',
                        //'institution_inquiries' => 'InstitutionInquiryListFilter'
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