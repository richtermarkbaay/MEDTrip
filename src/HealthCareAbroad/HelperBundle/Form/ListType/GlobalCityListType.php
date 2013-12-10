<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use HealthCareAbroad\HelperBundle\Services\LocationService;
use HealthCareAbroad\HelperBundle\Form\DataTransformer\CityTransformer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GlobalCityListType extends AbstractType 
{	
    const NAME = 'globalCity_list';
    
    /**
     * @var LocationService
     */
    private $service;
    
    public function __construct(LocationService $service)
    {
        $this->service = $service;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CityTransformer($this->service));
    }
    
	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }
   
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return self::NAME;
    }
}