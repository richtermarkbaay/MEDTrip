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
    
    /**
     * @var InstitutionService
     */
    private $institutionService;
    
    public function __construct(ContainerInterface $container=null)
    {
        $this->container = $container;
        $this->institutionService = $this->container->get('services.institution');
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