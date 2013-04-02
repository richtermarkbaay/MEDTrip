<?php

namespace HealthCareAbroad\SearchBundle\Services;

class NarrowSearchParameterBag extends SearchParameterBag
{
    private static $allowedKeys = array('specializationId', 'subSpecializationId', 'treatmentId', 'countryId', 'cityId');
    
    public function __construct(array $parameters = array())
    {
        
    }
    
    static public function getAllowedParameters()
    {
        
    }
}