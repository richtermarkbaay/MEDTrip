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
                $class = 'awards-icon'; break;

            case AdvertisementHighlightType::SERVICE:
                $class = 'about-icon'; break;

            case AdvertisementHighlightType::DOCTOR:
                $class = 'doctors-icon'; break;

            case AdvertisementHighlightType::TREATMENT:
                $class = 'about-icon'; break;

            case AdvertisementHighlightType::SPECIALIZATION:
                $class = 'about-icon'; break;

            case AdvertisementHighlightType::CLINIC:
                $class = 'about-icon'; break;
        }

        return $class . $color;
    }

    public function getName()
    {
        return 'frontend_advertisement_twig_extension';
    }
}