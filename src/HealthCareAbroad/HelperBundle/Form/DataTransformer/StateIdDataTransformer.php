<?php

namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use HealthCareAbroad\HelperBundle\Entity\State;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Component\Form\DataTransformerInterface;

class StateIdDataTransformer implements DataTransformerInterface
{
    /**
     * @var LocationService
     */
    private $locationService;
    
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }
    
    public function transform($entity)
    {
        if ($entity instanceof State){
            return $entity->getId();
        }
        
        return null;
    }
    
    public function reverseTransform($id)
    {
        if (!$id){
            return null;
        }
        
        $state = $this->locationService->findStateById($id);
        if (!$state){
            $globalStateData = $this->locationService->getGlobalStateById($id);
            if (null === $globalStateData){
                throw new \Exception('Failed to transform invalid state id: '.$id);
            }
            $globalStateData = $globalStateData['state'];
            $state = new State();
            $state->setId($globalStateData['id']);
            $state->setName($globalStateData['name']);
            
            $countryData = $globalStateData['geoCountry'];
            $country = $this->locationService->getCountryById($countryData['id']);
            
            $state->setCountry($country);
//             $this->locationService->saveState($state);
        }
        
        return $state;
    }
}