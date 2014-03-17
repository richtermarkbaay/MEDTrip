<?php

namespace HealthCareAbroad\HelperBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\SearchBundle\Services\SearchUrlGenerator;

use HealthCareAbroad\SearchBundle\Services\SearchStates;

use HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * service id: services.helper.page_meta_configuration
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class PageMetaConfigurationService
{
    /** contansts for known variables in a meta configuration pattern **/
    const CLINIC_RESULTS_COUNT_VARIABLE = 'clinic_results_count';
    
    const ACTIVE_CLINICS_COUNT_VARIABLE = 'active_clinics_count';
    
    const SPECIALIZATIONS_COUNT_VARIABLE = 'specializations_count_variable';
    
    const SPECIALIZATIONS_LIST_VARIABLE = 'specializations_list_variable';
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    /** 
     * @var string
     */
    private $siteName;
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;
    }
    
    /**
     * Find a PageMetaConfiguration by url
     * 
     * @param string $url
     * @return PageMetaConfiguration
     */
    public function findOneByUrl($url)
    {
        return $this->doctrine->getRepository('HelperBundle:PageMetaConfiguration')->findOneByUrl($url);   
    }
    
    /**
     * Persist to database
     * 
     * @param PageMetaConfiguration $pageMetaConfiguration
     */
    public function save(PageMetaConfiguration $pageMetaConfiguration)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($pageMetaConfiguration);
        $em->flush();
    }
    
    /**
     * Create a PageMetaConfiguration based on the current search objects
     * 
     * @param array $searchObjects
     * @return \HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration
     */
    public function buildFromSearchObjects(array $searchObjects = array())
    {
        $searchState = 0;
        $searchStateMapping = SearchStates::getSearchUrlParameterKeyToSearchStateValueMapping();
        $pageMetaVariables = array();
        foreach ($searchObjects as $key => $_searchObject) {
            $searchState += $searchStateMapping[$key];
            $pageMetaVariables[$key] = \is_object($_searchObject)
                    ? $_searchObject->getName()
                    : $_searchObject;
        }
        
        $searchStateValue = SearchStates::getSearchStateFromValue($searchState);
        $title = '';
        $description = '';
        $keywords = '';
        
        switch ($searchStateValue) {
            // singe searches - destination
            case SearchStates::COUNTRY_SEARCH:
            case SearchStates::CITY_SEARCH:
                $destinationArr = $this->_extractDestinationValues($pageMetaVariables);
                $destination = \implode(', ',$destinationArr);
                $title = 'Healthcare and Dental Clinics in '.$destination.' - '.$this->siteName;
                $description = 'Compare {'.PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE.'} clinics that offer medical specialties in '.$destination.'. '.$this->siteName.' is an unbiased international directory of Healthcare and Dental Clinics';
                $keywords = $destination.', Medical tourism, compare, international, abroad, doctor, dr, dentist';
                break;
            // single searches - specialization
            case SearchStates::SPECIALIZATION_SEARCH:
                $title = $pageMetaVariables[SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION].' - '.$this->siteName;
                $description = 'Compare {'.PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE.'} clinics that offer '. $pageMetaVariables[SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION] .' in different countries. '.$this->siteName.' is an unbiased international directory of Healthcare and Dental Clinics';
                $keywords = $pageMetaVariables[SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION].', Medical tourism, compare, international, abroad, doctor, dr, dentist';
                break;
            // single searches - Sub-specialization
            case SearchStates::SUB_SPECIALIZATION_SEARCH:
                $treatmentsArr = $this->_extractTreatmentValues($pageMetaVariables);
                $title = \implode(' - ', $treatmentsArr).' - '.$this->siteName;
                $description = 'Compare {'.PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE.'} clinics that offer '. $pageMetaVariables[SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION] .' in different countries. '.$this->siteName.' is an unbiased international directory of Healthcare and Dental Clinics';
                $keywords = \implode(', ', $treatmentsArr).', Medical tourism, compare, international, abroad, doctor, dr, dentist';
                break;
            // single searches - treatment
            case SearchStates::TREATMENT_SEARCH:
                $treatmentsArr = $this->_extractTreatmentValues($pageMetaVariables);
                $title = \implode(' - ', $treatmentsArr).' - '.$this->siteName;
                $description = 'Compare {'.PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE.'} '. $pageMetaVariables[SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT] .' clinics in different countries. '.$this->siteName.' is an unbiased international directory of Healthcare and Dental Clinics';
                $keywords = \implode(', ', $treatmentsArr).', Medical tourism, compare, international, abroad, doctor, dr, dentist';
                break;
            // combination search - destination + specialization
            case SearchStates::COUNTRY_SPECIALIZATION_SEARCH:
            case SearchStates::CITY_SPECIALIZATION_SEARCH:
                $destinationArr= $this->_extractDestinationValues($pageMetaVariables);
                $destination = \implode(', ', $destinationArr);
                $title = $pageMetaVariables[SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION].' in '.$destination.' - '.$this->siteName;
                $description = 'Compare {'.PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE.'} clinics that offer '. $pageMetaVariables[SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION] .' in '.$destination.'. '.$this->siteName.' is an unbiased international directory of Healthcare and Dental Clinics';
                $keywords = $destination.', '.$pageMetaVariables[SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION].', Medical tourism, compare, international, abroad, doctor, dr, dentist';
                break;
            case SearchStates::COUNTRY_SUB_SPECIALIZATION_SEARCH:
            case SearchStates::CITY_SUB_SPECIALIZATION_SEARCH:
                $destinationArr = $this->_extractDestinationValues($pageMetaVariables);
                $treatmentsArr = $this->_extractTreatmentValues($pageMetaVariables);
                $destination = \implode(', ', $destinationArr);
                $title = \implode(' - ', $treatmentsArr).' in '.$destination.' - '.$this->siteName;
                $description = 'Compare {'.PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE.'} clinics that offer '. $pageMetaVariables[SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION] .' in '.$destination.'. '.$this->siteName.' is an unbiased international directory of Healthcare and Dental Clinics';
                $keywords = $destination.', '.\implode(', ',$treatmentsArr).', Medical tourism, compare, international, abroad, doctor, dr, dentist';
                break;
            case SearchStates::COUNTRY_TREATMENT_SEARCH:
            case SearchStates::CITY_TREATMENT_SEARCH:
                $destinationArr = $this->_extractDestinationValues($pageMetaVariables);
                $treatmentsArr = $this->_extractTreatmentValues($pageMetaVariables);
                $destination = \implode(', ', $destinationArr);
                $title = \implode(' - ', $treatmentsArr).' in '.$destination.' - '.$this->siteName;
                $description = 'Compare {'.PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE.'} '. $pageMetaVariables[SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT] .' clinics in '.$destination.'. '.$this->siteName.' is an unbiased international directory of Healthcare and Dental Clinics';
                $keywords = $destination.', '.\implode(', ',$treatmentsArr).', Medical tourism, compare, international, abroad, doctor, dr, dentist';
                break;
        }
        
        $metaConfig = new PageMetaConfiguration();
        $metaConfig->setTitle($title);
        $metaConfig->setDescription($description);
        $metaConfig->setKeywords($keywords);
        $metaConfig->setPageType(PageMetaConfiguration::PAGE_TYPE_SEARCH_RESULTS);
        
        return $metaConfig;
    }
    
    /**
     * Create PageMetaConfiguration for hospital page
     * 
     * @param Mixed <Institution, array> $institution
     * @return \HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration
     */
    public function buildForInstitutionPage($institution)
    {
        $metaConfig = new PageMetaConfiguration();
        
        if ($institution instanceof Institution){
            $institutionName = $institution->getName();
            $location = ($institution->getCity() ? $institution->getCity().', ' : '').$institution->getCountry();
        }
        else {
            $institutionName = $institution['name'];
            $location = (isset($institution['city']) ? $institution['city']['name'].', ' : '').$institution['country']['name'];
        }
        
        // title: #HospitalName #city, #country - site_name
        $metaConfig->setTitle("{$institutionName} {$location} - " . $this->siteName);
        // description: #HospitalName in #city, #country offers treatments in #NumberOfSubSpecialities Specialities at #NumberOfClinics Clinics. Find your treatment at site_name
        $metaConfig->setDescription("{$institutionName} in {$location} offers treatments in {".PageMetaConfigurationService::SPECIALIZATIONS_COUNT_VARIABLE."} Specialties at {".PageMetaConfigurationService::ACTIVE_CLINICS_COUNT_VARIABLE."} Clinics. Find your treatment at " . $this->siteName);
        // keywords: #HospitalName, #City, #Country,  (up to 10) #Sp`eciality, medical tourism, Doctor, Dentist
        $metaConfig->setKeywords("{$institutionName}, {$location}, {".PageMetaConfigurationService::SPECIALIZATIONS_LIST_VARIABLE."}, medical tourism, Doctor, Dentist");
        $metaConfig->setPageType(PageMetaConfiguration::PAGE_TYPE_INSTITUTION);
        
        return $metaConfig;
    }
    
    /**
     * Create a PageMetaConfiguration for clinic page
     * 
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @return \HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration
     */
    public function buildForInstitutionMedicalCenterPage(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        $institution = $institutionMedicalCenter->getInstitution();
        $location = ($institution->getCity() ? $institution->getCity().', ' : '').$institution->getCountry();
        
        $metaConfig = new PageMetaConfiguration();
        $metaConfig->setTitle("{$institutionMedicalCenter->getName()} - {$institution->getName()} {$location} - " . $this->siteName);
        $metaConfig->setDescription("{$institutionMedicalCenter->getName()} at {$institution->getName()} offers treatments in {".PageMetaConfigurationService::SPECIALIZATIONS_LIST_VARIABLE."} in {$location}. Get details at " . $this->siteName);
        $metaConfig->setKeywords("{$institution->getName()}, {$institutionMedicalCenter->getName()}, {$location}, {".PageMetaConfigurationService::SPECIALIZATIONS_LIST_VARIABLE."}, medical tourism, Doctor, Dentist");
        $metaConfig->setPageType(PageMetaConfiguration::PAGE_TYPE_INSTITUTION_MEDICAL_CENTER);
        
        return $metaConfig;
    }
    
    static public function getKnownVariables()
    {
        return array(
            PageMetaConfigurationService::CLINIC_RESULTS_COUNT_VARIABLE,
            PageMetaConfigurationService::ACTIVE_CLINICS_COUNT_VARIABLE,
            PageMetaConfigurationService::SPECIALIZATIONS_COUNT_VARIABLE,
            PageMetaConfigurationService::SPECIALIZATIONS_LIST_VARIABLE,
        );
    }
    
    private function _extractDestinationValues($pageMetaVariables)
    {
        $destinationArr= array();
        $destinationKeys = array(SearchUrlGenerator::SEARCH_URL_PARAMETER_CITY, SearchUrlGenerator::SEARCH_URL_PARAMETER_COUNTRY);
        foreach ($destinationKeys as $_key) {
            if (isset($pageMetaVariables[$_key])) {
                $destinationArr[] = $pageMetaVariables[$_key];
            }    
        }
        
        return $destinationArr;
    }
    
    private function _extractTreatmentValues($pageMetaVariables)
    {
        $keys = array(SearchUrlGenerator::SEARCH_URL_PARAMETER_TREATMENT, SearchUrlGenerator::SEARCH_URL_PARAMETER_SUB_SPECIALIZATION, SearchUrlGenerator::SEARCH_URL_PARAMETER_SPECIALIZATION);
        $arr = array();
        foreach ($keys as $_key) {
            if (isset($pageMetaVariables[$_key])) {
                $arr[] = $pageMetaVariables[$_key];
            }
        }
        
        return $arr;
    }
}