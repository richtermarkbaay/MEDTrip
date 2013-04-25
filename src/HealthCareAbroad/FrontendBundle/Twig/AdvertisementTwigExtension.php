<?php
namespace HealthCareAbroad\FrontendBundle\Twig;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementHighlightType;


class AdvertisementTwigExtension extends \Twig_Extension
{
    
    public function getFunctions()
    {
        return array(
            'ad_highlight_placeholder_class' => new \Twig_Function_Method($this, 'getAdHighlightPlaceholderClass')
        );
    }

    public function getAdHighlightPlaceholderClass($type, $color = '')
    {
        if($color) $color = "-$color";

        switch($type)
        {
            case AdvertisementHighlightType::AWARD:
                $class = 'award-icon'; break;

            case AdvertisementHighlightType::SERVICE:
                $class = 'service-icon'; break;

            case AdvertisementHighlightType::DOCTOR:
                $class = 'doctor-icon'; break;

            case AdvertisementHighlightType::TREATMENT:
                $class = 'treatment-icon'; break;

            case AdvertisementHighlightType::SPECIALIZATION:
                $class = 'specialization-icon'; break;

            case AdvertisementHighlightType::CLINIC:
                $class = 'clinic-icon'; break;
        }

        return "hca-sprite $class" . $color;
    }

    public function getName()
    {
        return 'frontend_advertisement_twig_extension';
    }
}