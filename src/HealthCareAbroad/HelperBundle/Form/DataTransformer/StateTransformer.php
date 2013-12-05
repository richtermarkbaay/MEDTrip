<?php

namespace HealthCareAbroad\HelperBundle\Form\DataTransformer;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\HelperBundle\Entity\State;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Component\Form\DataTransformerInterface;

class StateTransformer implements DataTransformerInterface
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
        
        $state = $this->locationService->getStateById($id);

        if (!$state){ 
            $globalStateData = $this->locationService->getGlobalStateById($id);
            if (null === $globalStateData){
                throw new \Exception('Failed to transform invalid state id: '.$id);
            }

            $state = $this->locationService->createStateFromArray($globalStateData);
        }
        
        return $state;
    }
}