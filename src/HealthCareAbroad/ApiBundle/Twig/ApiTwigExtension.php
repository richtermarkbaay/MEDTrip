<?php

namespace HealthCareAbroad\ApiBundle\Twig;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

class ApiTwigExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'api_twig_extension';
    }
    
    public function getFunctions()
    {
        return array(
        );
    }
}