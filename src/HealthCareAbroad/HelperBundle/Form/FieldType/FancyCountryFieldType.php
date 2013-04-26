<?php
namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryTransformer;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CountryArrayTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FancyCountryFieldType extends AbstractType
{
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
         
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'fancy_country';
    }
}