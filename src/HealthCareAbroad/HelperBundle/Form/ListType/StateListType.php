<?php

namespace HealthCareAbroad\HelperBundle\Form\ListType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\StateTransformer;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\AbstractType;

class StateListType extends AbstractType
{
    /**
     * @var LocationService
     */
    private $locationService;
    
    public function __construct(LocationService $service)
    {
        $this->locationService = $service;
    }
    
    public function getName()
    {
        return 'state_list';
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options=array())
    {
        $builder->addModelTransformer(new StateTransformer($this->locationService));   
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }
    
    public function getParent()
    {
        return 'text';
    }
    
}