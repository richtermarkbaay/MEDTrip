<?php

namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use HealthCareAbroad\HelperBundle\Entity\City;

use Symfony\Component\Form\DataTransformerInterface;

class CityTransformer implements DataTransformerInterface
{
    public function __construct(LocationService $service)
    {
        $this->service = $service;
    }
    
    public function transform($data)
    {
        if ($data instanceof City) {
            $data = $data->getId();
        }
        
        return $data;
    }
    
    public function reverseTransform($id)
    {
        if(!$id){
            return null;
        }

        $city = $this->service->getCityById($id);
        if (!$city) {
            $cityGlobalData = $this->service->getGlobalCityById($id);
            $city = $this->service->createCityFromArray($cityGlobalData); 
        }

        return $city;
    }
}