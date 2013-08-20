<?php

namespace HealthCareAbroad\StatisticsBundle\Twig;

use HealthCareAbroad\StatisticsBundle\Exception\TrackerException;

use HealthCareAbroad\StatisticsBundle\Form\TrackerFormType;

use Symfony\Component\Form\FormFactory;

use HealthCareAbroad\FrontendBundle\Services\FrontendRouteService;

use HealthCareAbroad\HelperBundle\Services\LocationService;

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
     * @var LocationService
     */
    private $locationService;
    
    /**
     * @var FormFactory
     */
    private $formFactory;
    
    /**
     * @var \Twig_Environment
     */
    private $twig;
    
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
    
    public function getLocationService()
    {
        if (is_null($this->locationService)) {
            $this->locationService = $this->container->get('services.location');
        }
        
        return $this->locationService;
    }
    
    /**
     * @return \Symfony\Component\Form\FormFactory
     */
    public function getFormFactory()
    {
        if (\is_null($this->formFactory)) {
            $this->formFactory = $this->container->get('form.factory');
        }
        
        return $this->formFactory;
    }
    
    /**
     * @return Twig_Environment
     */
    public function getTwig()
    {
        if (\is_null($this->twig)) {
            $this->twig = $this->container->get('twig');
        }
        
        return $this->twig;
    }
    
    public function getFunctions()
    {
        return array(
            'statistics_store_impressions' => new \Twig_Function_Method($this, 'storeImpressions'),
            'encode_advertisement_impressions_parameters' => new \Twig_Function_Method($this, 'encode_advertisement_impressions_parameters'),
            'encode_advertisement_clickthrough_parameters' => new \Twig_Function_Method($this, 'encode_advertisement_clickthrough_parameters'),
            'encode_search_result_item_clickthrough_parameters' => new \Twig_Function_Method($this, 'encode_search_result_item_clickthrough_parameters'),
            'encode_search_result_item_impression_parameters' => new \Twig_Function_Method($this, 'encode_search_result_item_impression_parameters'),
            'get_statistics_parameter_attribute_name' => new \Twig_Function_Method($this, 'get_statistics_parameter_attribute_name'),
            'get_clickthrough_tracker_class' => new \Twig_Function_Method($this, 'get_clickthrough_tracker_class'),
            'get_impression_tracker_class' => new \Twig_Function_Method($this, 'get_impression_tracker_class'),
            'get_impression_tracker_form_id' => new \Twig_Function_Method($this, 'get_impression_tracker_form_id'),
            'get_clickthrough_tracker_form_id' => new \Twig_Function_Method($this, 'get_clickthrough_tracker_form_id'),
            'render_frontend_statistics_tracker_form' => new \Twig_Function_Method($this, 'render_frontend_statistics_tracker_form'),
        );
    }
    
    public function render_frontend_statistics_tracker_form()
    {
        $form = $this->getFormFactory()->create(new TrackerFormType());
        
        return  $this->getTwig()->render('StatisticsBundle:Tracker:form.html.twig', array('statsTrackerForm' => $form->createView()));
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
    
    public function encode_advertisement_impressions_parameters($advertisement)
    {
        $parameters = $this->_getCommonAdvertisementStatisticParameters($advertisement);
        $parameters[StatisticParameters::CATEGORY_ID] = StatisticCategories::ADVERTISEMENT_IMPRESSIONS;
        
        return StatisticParameters::encodeParameters($parameters);
    }
    
    public function encode_advertisement_clickthrough_parameters($advertisement)
    {
        $parameters = $this->_getCommonAdvertisementStatisticParameters($advertisement);
        $parameters[StatisticParameters::CATEGORY_ID] = StatisticCategories::ADVERTISEMENT_CLICKTHROUGHS;
        
        return StatisticParameters::encodeParameters($parameters);
    }
    
    private function _getCommonAdvertisementStatisticParameters($advertisement)
    {
        if ($advertisement instanceof AdvertisementDenormalizedProperty) {
            // transform to array data
            $advertisementData = array(
                'id' => $advertisement->getId(),
                'institution' => array(
                    'id' => $advertisement->getInstitution()->getId()
                )
            );
        }
        else {
            if (!\is_array($advertisement)) {
                // unknown hydration type
                throw TrackerException::unknownAdvertisementDataType();
            }
            
            $advertisementData = $advertisement;
        }
        
        $parameters = array(
            StatisticParameters::ADVERTISEMENT_ID => $advertisementData['id'],
            StatisticParameters::INSTITUTION_ID => $advertisementData['institution']['id'],
            StatisticParameters::TYPE => StatisticTypes::ADVERTISEMENT,
        );
        
        return $parameters;
    }
    
    /** end advertisement statistic parameters encoders **/
    
    /** Start Search Result Item parameter encoders **/
    
    /**
     * Generate an encoded string containing the necessary parameters for Search Result/Listing clickthrough stats tracker
     * 
     * @param Mixed $searchResultItem
     * @param array $routeParameters
     */
    public function encode_search_result_item_clickthrough_parameters($searchResultItem)
    {
        $encodedParameters = '';
        // only instances of InstitutionMedicalCenter and Institution will be accepted as searchResultItem
        if ($searchResultItem instanceof InstitutionMedicalCenter || $searchResultItem instanceof Institution) {
            $parameters = $this->_getCommonSearchResultItemStatisticsParameters($searchResultItem, $searchResultItem instanceof InstitutionMedicalCenter);
            $parameters[StatisticParameters::CATEGORY_ID] = StatisticCategories::SEARCH_RESULTS_PAGE_ITEM_CLICKTHROUGHS;
            $encodedParameters = StatisticParameters::encodeParameters($parameters);
        }
        
        return $encodedParameters;   
    }
    
    public function encode_search_result_item_impression_parameters($searchResultItem)
    {
        $encodedParameters = '';
        // only instances of InstitutionMedicalCenter and Institution will be accepted as searchResultItem
        if ($searchResultItem instanceof InstitutionMedicalCenter || $searchResultItem instanceof Institution) {
            $parameters = $this->_getCommonSearchResultItemStatisticsParameters($searchResultItem, $searchResultItem instanceof InstitutionMedicalCenter);
            $parameters[StatisticParameters::CATEGORY_ID] = StatisticCategories::SEARCH_RESULTS_PAGE_ITEM_IMPRESSIONS;
            $encodedParameters = StatisticParameters::encodeParameters($parameters);
        }
        
        return $encodedParameters;
    }
    
    private function _getCommonSearchResultItemStatisticsParameters($searchResultItem, $isMedicalCenterContext)
    {
        $parameters = $this->_mapSearchResultRouteParameters();
        
        if ($isMedicalCenterContext) {
            $parameters[StatisticParameters::INSTITUTION_ID] = $searchResultItem->getInstitution()->getId();
            $parameters[StatisticParameters::INSTITUTION_MEDICAL_CENTER_ID] = $searchResultItem->getId();
        }
        else {
            $parameters[StatisticParameters::INSTITUTION_ID] = $searchResultItem->getId();
        }
        $parameters[StatisticParameters::TYPE] = StatisticTypes::SEARCH_RESULT_ITEM;
        return $parameters;
    }
    
    // map the search result route parameters to statistic parameters with values
    private function _mapSearchResultRouteParameters()
    {
        $routeAttributes = $this->container->get('request')->attributes;
        $routeParameters = $routeAttributes->get('_route_params');
        if (FrontendRouteService::COMBINED_SEARCH_ROUTE_NAME == $routeAttributes->get('_route')) {
            // combined search depends on the frontend router service to get the values for the route slugs
            $routeParamKeyMapping = array(
                'specializationId' => StatisticParameters::SPECIALIZATION_ID,
                'subSpecializationId' => StatisticParameters::SUB_SPECIALIZATION_ID,
                'treatmentId' => StatisticParameters::TREATMENT_ID,
                'countryId' => StatisticParameters::COUNTRY_ID,
                'cityId' => StatisticParameters::CITY_ID
            );
            $statisticsParameters = array();
            foreach ($routeParameters as $key => $value) {
                $statisticsParameters[$routeParamKeyMapping[$key]] = $value;
            }
        }
        // assume that single searches will depend mostly on slugs
        else {
            $routeParamKeyMapping = array(
                'specialization' => StatisticParameters::SPECIALIZATION_ID,
                'subSpecialization' => StatisticParameters::SUB_SPECIALIZATION_ID,
                'treatment' => StatisticParameters::TREATMENT_ID,
                'country' => StatisticParameters::COUNTRY_ID,
                'city' => StatisticParameters::CITY_ID,
            );
            
            $finders = array(
                StatisticParameters::SPECIALIZATION_ID => array($this->getTreatmentBundleService(), 'getSpecializationBySlug'),
                StatisticParameters::TREATMENT_ID => array($this->getTreatmentBundleService(),'getTreatmentBySlug'),
                StatisticParameters::SUB_SPECIALIZATION_ID => array($this->getTreatmentBundleService(),'getSubSpecializationBySlug'),
                StatisticParameters::COUNTRY_ID => array($this->getLocationService(),'getCountryBySlug'),
                StatisticParameters::CITY_ID => array($this->getLocationService(),'getCityBySlug'),
            );
            
            $passedParameters = \array_intersect_key($routeParameters, $routeParamKeyMapping);
            $statisticsParameters = array();
            // get the ids of the passed slugs
            foreach ($passedParameters as $key => $slug) {
                if (isset($finders[$routeParamKeyMapping[$key]])) {
                    // get the object by slug
                    $obj = $finders[$routeParamKeyMapping[$key]][0]->{$finders[$routeParamKeyMapping[$key]][1]}($slug);
                    if ($obj) {
                        $statisticsParameters[$routeParamKeyMapping[$key]] = $obj->getId();
                    }
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