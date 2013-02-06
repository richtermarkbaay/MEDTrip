<?php

namespace HealthcareAbroad\StatisticsBundle\Twig;

class StatisticsTwigExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return array(
                        'statistics_store_impressions' => new \Twig_Function_Method($this, 'storeImpressions')
                        );
    }
    
    public function storeImpressions()
    {
        
    }
    
    public function getName()
    {
        return 'statistics_twig_extension';
    }
}