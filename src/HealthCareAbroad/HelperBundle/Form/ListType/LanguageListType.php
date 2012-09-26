<?php
namespace HealthCareAbroad\InstitutionBundle\Form\Transformer;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

use Symfony\Component\DependencyInjection\ContainerInterface;

use HealthCareAbroad\AdminBundle\Entity\Language;

use Symfony\Component\Form\DataTransformerInterface;

class LanguageTransformer implements DataTransformerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var InstitutionService
     */
    private $languageService;
    
    public function __construct(ContainerInterface $container=null)
    {
        $this->container = $container;
        $this->languageService = $this->container->get('services.language');
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