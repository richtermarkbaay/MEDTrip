<?php

namespace HealthCareAbroad\SearchBundle\Exception;

class SearchUrlGeneratorException extends \Exception
{
    public static function requiredRouteName()
    {
        return new self('Search URL generator requires a route name');
    }
    
    public static function unknownRoute($route)
    {
        return new self("Unknown search url route {$route}");
    }
    
    public static function missingMandatoryVariable($variable, $route)
    {
        return new self("Missing mandatory variable: {$variable} for search url route: {$route}");
    }
    
}