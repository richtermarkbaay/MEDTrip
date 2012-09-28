<?php
namespace HealthCareAbroad\InstitutionBundle\Form\Transformer;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use Symfony\Component\DependencyInjection\ContainerInterface;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Component\Form\DataTransformerInterface;

class InstitutionTransformer implements DataTransformerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
        
    public function transform($institution)
    {
        return $institution->getId();
    }
    
    public function reverseTransform($id)
    {
        return $this->institutionService->findById($id);
    }
}