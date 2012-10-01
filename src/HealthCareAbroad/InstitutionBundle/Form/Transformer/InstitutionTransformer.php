<?php
namespace HealthCareAbroad\InstitutionBundle\Form\Transformer;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use Symfony\Component\DependencyInjection\ContainerInterface;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Symfony\Component\Form\DataTransformerInterface;

class InstitutionTransformer implements DataTransformerInterface
{
    /**
     * @var InstitutionService
     */
    private $institutionService;
    
    public function setInstitutionService(InstitutionService $service)
    {
        $this->institutionService = $service;
    }
    
    public function transform($institution)
    {
        return $institution->getId();
    }
    
    public function reverseTransform($id)
    {
        return $this->institutionService->findById($id);
    }
}