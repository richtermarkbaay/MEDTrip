<?php
namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FancyCountryFieldType extends AbstractType
{
    public function getName()
    {
        return 'fancy_country';
    }
    
    /**
     * @var LocationService
     */
    private $locationService;

    public function setLocationService(LocationService $service)
    {
        $this->locationService = $service;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CountryTransformer($this->locationService));
    }
   
    public function getParent()
    {
        return 'hidden';
    }
    
}