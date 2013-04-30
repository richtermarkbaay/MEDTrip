<?php

namespace HealthCareAbroad\HelperBundle\Form\FieldType;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\BusinessHourEntityViewTransformer;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\HelperBundle\Form\DataTransformer\BusinessHourEntityDataTransformer;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class BusinessHourEntityFieldType extends AbstractType
{
    private $owner;
    public function __construct(InstitutionMedicalCenter $owner=null)
    {
        //$this->owner = $owner;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new BusinessHourEntityViewTransformer());
        $builder->addModelTransformer(new BusinessHourEntityDataTransformer());
        
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'HealthCareAbroad\InstitutionBundle\Entity\BusinessHour'
        ));
    }
    
    public function getParent()
    {
        return 'hidden';
    }
    
    public function getName()
    {
        return 'business_hour_entity_type';
    }
}