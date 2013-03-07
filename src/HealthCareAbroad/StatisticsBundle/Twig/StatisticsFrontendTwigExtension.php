<?php

namespace HealthCareAbroad\StatisticsBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\StatisticsBundle\Services\StatisticsParameterBag;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

use HealthCareAbroad\TreatmentBundle\Services\TreatmentBundleService;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticCategories;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticParameters;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementDenormalizedProperty;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

class StatisticsFrontendTwigExtension extends \Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var TreatmentBundleService
     */
    private $treatmentBundleService = null;
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    public function setContainer(ContainerInterface $container=null)
    {
        $this->container = $container;
        return $this;
    }
    
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * @return \HealthCareAbroad\TreatmentBundle\Services\TreatmentBundleService
     */
    public function getTreatmentBundleService()
    {
        if (is_null($this->treatmentBundleService)) {
            $this->treatmentBundleService = $this->container->get('services.treatment_bundle');
        }
        
        return $this->treatmentBundleService;
    }
    
    public function getFunctions()
    {
        return array(
            'statistics_store_impressions' => new \Twig_Function_Method($this, 'storeImpressions'),
            'encode_advertisement_impressions_parameters' => new \Twig_Function_Method($this, 'encode_advertisement_impressions_parameters'),
            'encode_advertisement_clickthrough_parameters' => new \Twig_Function_Method($this, 'encode_advertisement_clickthrough_parameters'),
            'encode_search_result_item_clickthrough_parameters' => new \Twig_Function_Method($this, 'encode_search_result_item_clickthrough_parameters'),
            'get_statistics_parameter_attribute_name' => new \Twig_Function_Method($this, 'get_statistics_parameter_attribute_name'),
            'get_clickthrough_tracker_class' => new \Twig_Function_Method($this, 'get_clickthrough_tracker_class'),
            'get_impression_tracker_class' => new \Twig_Function_Method($this, 'get_impression_tracker_class'),
            'get_impression_tracker_form_id' => new \Twig_Function_Method($this, 'get_impression_tracker_form_id'),
            'get_clickthrough_tracker_form_id' => new \Twig_Function_Method($this, 'get_clickthrough_tracker_form_id')
        );
    }
    
    public function get_impression_tracker_form_id()
    {
        return 'hca_impressions_tracker_form';
    }
    
    public function get_clickthrough_tracker_form_id()
    {
        return 'hca_clickthrough_tracker_form';
    }
    
    public function get_clickthrough_tracker_class()
    {
        return 'hca_statistics_clickthroughs';
    }
    
    public function get_impression_tracker_class()
    {
        return 'hca_statistics_impressions';
    }
    
    /**
     * Get the attribute name for the holder of the statistics parameters
     * 
     * @return string
     */
    public function get_statistics_parameter_attribute_name()
    {
        return 'data-statistic_parameters';
    }
    
    /** advertisement statistic parameters encoders **/
    
    public function encode_advertisement_impressions_parameters(AdvertisementDenormalizedProperty $advertisement)
    {
        $parameters = $this->_getCommonAdvertisementStatisticParameters($advertisement);
        $parameters[StatisticParameters::CATEGORY_ID] = StatisticCategories::ADVERTISEMENT_IMPRESSIONS;
        
        return StatisticParameters::encodeParameters($parameters);
    }
    
    public function encode_advertisement_clickthrough_parameters(AdvertisementDenormalizedProperty $advertisement)
    {
        $parameters = $this->_getCommonAdvertisementStatisticParameters($advertisement);
        $parameters[StatisticParameters::CATEGORY_ID] = StatisticCategories::ADVERTISEMENT_CLICKTHROUGHS;
        
        return StatisticParameters::encodeParameters($parameters);
    }
    
    private function _getCommonAdvertisementStatisticParameters(AdvertisementDenormalizedProperty $advertisement)
    {
        return $parameters = array(
            StatisticParameters::ADVERTISEMENT_ID => $advertisement->getId(),
            StatisticParameters::INSTITUTION_ID => $advertisement->getInstitution()->getId(),
            StatisticParameters::TYPE => StatisticTypes::ADVERTISEMENT,
        );
    }
    
    /** end advertisement statistic parameters encoders **/
    
    /** Start Search Result Item parameter encoders **/
    
    /**
     * Generate an encoded string containing the necessary parameters for Search Result/Listing clickthrough stats tracker
     * 
     * @param Mixed $searchResultItem
     * @param array $routeParameters
     */
    public function encode_search_result_item_clickthrough_parameters($searchResultItem, $routeParameters = array())
    {
        $encodedParameters = '';
        // only instances of InstitutionMedicalCenter and Institution will be accepted as searchResultItem
        if ($searchResultItem instanceof InstitutionMedicalCenter || $searchResultItem instanceof Institution) {
            $isMedicalCenterContext = $searchResultItem instanceof InstitutionMedicalCenter;
            $parameters = $this->_mapSearchResultRouteParameters($routeParameters);
            
            if ($isMedicalCenterContext) {
                $parameters[StatisticParameters::INSTITUTION_ID] = $searchResultItem->getInstitution()->getId();
                $parameters[StatisticParameters::INSTITUTION_MEDICAL_CENTER_ID] = $searchResultItem->getId();
            }
            else {
                $parameters[StatisticParameters::INSTITUTION_ID] = $searchResultItem->getId();
            }
            $parameters[StatisticParameters::CATEGORY_ID] = StatisticCategories::SEARCH_RESULTS_PAGE_ITEM_CLICKTHROUGHS;
            $parameters[StatisticParameters::TYPE] = StatisticTypes::SEARCH_RESULT_ITEM;
            
            $encodedParameters = StatisticParameters::encodeParameters($parameters);
        }
        
        return $encodedParameters;   
    }
    
    // map the search result route parameters to statistic parameters with values
    private function _mapSearchResultRouteParameters($routeParameters=array())
    {
        $routeParamKeyMapping = array(
            StatisticParameters::SPECIALIZATION_ID => 'specialization',
            StatisticParameters::SUB_SPECIALIZATION_ID => 'subSpecialization',
            StatisticParameters::TREATMENT_ID => 'treatment',
            StatisticParameters::COUNTRY_ID => 'country',
            StatisticParameters::CITY_ID => 'city'
        );
        
        $finders = array(
            StatisticParameters::SPECIALIZATION_ID => array($this->getTreatmentBundleService(), 'getSpecializationBySlug'),
            StatisticParameters::TREATMENT_ID => array($this->getTreatmentBundleService(),'getTreatmentBySlug'),
            StatisticParameters::SUB_SPECIALIZATION_ID => array($this->getTreatmentBundleService(),'getSubSpecializationBySlug')
        );
        
        $passedParameters = \array_intersect_key($routeParameters, \array_flip($routeParamKeyMapping));
        $flippedKeyMapping = \array_flip($routeParamKeyMapping);
        $statisticsParameters = array();
        // get the ids of the passed slugs
        foreach ($passedParameters as $key => $slug) {
            if (isset($finders[$flippedKeyMapping[$key]])) {
                // get the object by slug
                $obj = $finders[$flippedKeyMapping[$key]][0]->{$finders[$flippedKeyMapping[$key]][1]}($slug);
                if ($obj) {
                    $statisticsParameters[$flippedKeyMapping[$key]] = $obj->getId();
                }
            } 
        }
        
        return $statisticsParameters;
    }
    
    private function _mapFinders()
    {
        
    }
    /** End Search Result Item parameter encoders **/
    
    
    public function getName()
    {
        return 'statistics_twig_extension';
    }
}