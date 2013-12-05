<?php

namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Component\Form\DataTransformerInterface;

class CountryTransformer implements DataTransformerInterface
{
    private $service;
    
    public function __construct(LocationService $service)
    {
        $this->service = $service;
    }
    
    public function transform($data)
    {
        if ($data instanceof Country) {
            return $data->getId();
        }
        return $data;
    }
    
    public function reverseTransform($data)
    {
        
        if (\is_null($data)) {
            return $data;
        }
        $country = $this->service->getCountryById($data);
        if (!$country) {
            throw new \Exception('Cannot transform invalid country id');
        }

        return $country;
    }
}
