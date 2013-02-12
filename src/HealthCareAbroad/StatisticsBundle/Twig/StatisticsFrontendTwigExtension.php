<?php

namespace HealthCareAbroad\StatisticsBundle\Twig;

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
            'encode_advertisement_statistics_parameters' => new \Twig_Function_Method($this, 'encode_advertisement_statistics_parameters'),
            'get_statistics_parameter_attribute_name' => new \Twig_Function_Method($this, 'get_statistics_parameter_attribute_name'),
            'get_clickthrough_tracker_class' => new \Twig_Function_Method($this, 'get_clickthrough_tracker_class'),
            'get_impression_tracker_class' => new \Twig_Function_Method($this, 'get_impression_tracker_class'),
            'get_impression_tracker_form_id' => new \Twig_Function_Method($this, 'get_impression_tracker_form_id'),
        );
    }
    
    public function get_impression_tracker_form_id()
    {
        return 'hca_impressions_tracker_form';
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
    
    public function encode_advertisement_statistics_parameters(AdvertisementDenormalizedProperty $advertisement)
    {
        $parameters = array(
            StatisticParameters::ADVERTISEMENT_ID => $advertisement->getId(),
            StatisticParameters::INSTITUTION_ID => $advertisement->getInstitution()->getId(),
            StatisticParameters::TYPE => StatisticTypes::ADVERTISEMENT
        );
        
        return StatisticParameters::encodeParameters($parameters);
    }
    
    public function storeImpressions()
    {
        
    }
    
    public function getName()
    {
        return 'statistics_twig_extension';
    }
}