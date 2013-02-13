<?php

namespace HealthCareAbroad\StatisticsBundle\Twig;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticCategories;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticTypes;

use HealthCareAbroad\StatisticsBundle\Entity\StatisticParameters;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementDenormalizedProperty;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

class StatisticsFrontendTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
            'statistics_store_impressions' => new \Twig_Function_Method($this, 'storeImpressions'),
            'encode_advertisement_impressions_parameters' => new \Twig_Function_Method($this, 'encode_advertisement_impressions_parameters'),
            'encode_advertisement_clickthrough_parameters' => new \Twig_Function_Method($this, 'encode_advertisement_clickthrough_parameters'),
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
    
    
    
    public function getName()
    {
        return 'statistics_twig_extension';
    }
}