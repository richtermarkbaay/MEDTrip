<?php
namespace HealthCareAbroad\HelperBundle\Form\ListType;

use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\FormEvent;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\CityTransformer;

use Symfony\Component\Form\FormBuilderInterface;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CityListType extends AbstractType 
{	
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
        $resolver->setDefaults(array(
            'empty_value' => '<- select city ->',
            'attr' => array('class' => 'city_dropdown'),
            'choices' => array()
        ));
    }
   
    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'city_list';
    }
}